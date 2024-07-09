import React, { useEffect, useState } from "react";
import { createRoot } from "react-dom/client";
import { DateRange } from "@iroomit/react-date-range";
import tr from "date-fns/locale/tr";

import "@iroomit/react-date-range/dist/styles.css";
import "@iroomit/react-date-range/dist/theme/default.css";

export default function DatePicker() {
    const queryParameters = new URLSearchParams(window.location.search);
    const fromQuery = queryParameters.get("from");
    const toQuery = queryParameters.get("to");
    const [ranges, setRanges] = React.useState([
        {
            startDate: fromQuery ? new Date(fromQuery) : undefined,
            endDate: toQuery ? new Date(toQuery) : undefined,
            key: "selection",
        },
    ]);

    const [lang, setLang] = useState("en");
    const [translations, setTranslations] = useState();

    const [dates, setDates] = useState({
        from: undefined,
        to: undefined,
    });

    useEffect(() => {
        setLang(
            document.getElementById("date-picker").getAttribute("data-lang")
        );
        setTranslations(
            JSON.parse(
                document
                    .getElementById("date-picker")
                    .getAttribute("data-translations")
            )
        );
    }, []);

    useEffect(() => {
        const from = ranges[0]?.startDate;
        const to = ranges[0]?.endDate;

        if (from && to) {
            const day = from.getDate();
            const month = from.getMonth();
            const year = from.getFullYear();

            const toDay = to.getDate();
            const toMonth = to.getMonth();
            const toYear = to.getFullYear();

            setDates({
                from: `${month + 1}-${day}-${year}`,
                to: `${toMonth + 1}-${toDay}-${toYear}`,
            });
        }
    }, [ranges]);

    return (
        <>
            <DateRange
                startDatePlaceholder={translations?.from}
                endDatePlaceholder={translations?.to}
                dateDisplayFormat="dd-MM-yyyy"
                editableDateInputs={true}
                locale={lang === "tr" ? tr : undefined}
                ranges={ranges}
                onChange={(item) => {
                    setRanges([item.selection]);
                }}
            />
            <input hidden name="from" value={dates?.from} />
            <input hidden name="to" value={dates?.to} />
        </>
    );
}

if (document.getElementById("date-picker")) {
    createRoot(document.getElementById("date-picker")).render(<DatePicker />);
}
