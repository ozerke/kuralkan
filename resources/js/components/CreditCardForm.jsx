import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import Cards from "react-credit-cards-2";
import "react-credit-cards-2/dist/es/styles-compiled.css";

export default function CreditCardForm() {
    const [state, setState] = useState({
        number: "",
        expiry: "",
        cvc: "",
        name: "",
        focus: "",
    });

    const handleInputChange = (evt) => {
        const { name, value } = evt.target;

        switch (name) {
            case "number":
                const trimmedValue =
                    value.length > 16 ? value.substring(0, 16) : value;
                setState((prev) => ({
                    ...prev,
                    [name]: trimmedValue,
                }));
                break;
            case "expiry":
                let expiryDate =
                    value.length > 6 ? value.substring(0, 7) : value;
                expiryDate = expiryDate.replace(/[^0-9/]/g, "");

                if (expiryDate.length === 2) {
                    if (expiryDate[0] !== "0" && expiryDate[0] !== "1") {
                        let str = expiryDate.split("");
                        str[0] = "0";
                        str = str.join("");
                        expiryDate = str;
                    }
                }

                if (expiryDate.length > 2 && !expiryDate.includes("/")) {
                    let str = expiryDate.split("");
                    const copy = str[2];
                    str[2] = "/";
                    str[3] = copy;
                    str = str.join("");
                    expiryDate = str;
                }
                setState((prev) => ({
                    ...prev,
                    [name]: expiryDate,
                }));
                break;
            case "cvc":
                const cvc = value.length > 3 ? value.substring(0, 3) : value;
                setState((prev) => ({
                    ...prev,
                    [name]: cvc,
                }));
                break;
            default:
                setState((prev) => ({
                    ...prev,
                    [name]: value,
                }));
                break;
        }
    };

    const handleInputFocus = (evt) => {
        setState((prev) => ({ ...prev, focus: evt.target.name }));
    };

    const element = document.getElementById("credit-card-form");
    const translations = JSON.parse(element.getAttribute("data-translations"));

    return (
        <div className="flex flex-col w-full gap-8">
            <div className="flex w-full credit-card-flex-group justify-between gap-8">
                <div className="flex flex-col gap-4 justify-center">
                    <input
                        className="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        type="text"
                        name="name"
                        placeholder={translations?.name}
                        onWheel={(e) => e.currentTarget.blur()}
                        value={state.name}
                        onChange={handleInputChange}
                        onFocus={handleInputFocus}
                        onBlur={() =>
                            setState((prev) => ({ ...prev, focus: "" }))
                        }
                    />
                    <input
                        className="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        type="number"
                        name="number"
                        placeholder={translations?.number}
                        onWheel={(e) => e.currentTarget.blur()}
                        value={state.number}
                        onChange={handleInputChange}
                        onFocus={handleInputFocus}
                        onBlur={() =>
                            setState((prev) => ({ ...prev, focus: "" }))
                        }
                    />
                    <div className="flex flex-col lg:flex-row gap-4 lg:gap-2 w-full">
                        <input
                            className="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black flex-[0.5]"
                            type="text"
                            name="expiry"
                            placeholder={translations?.expiry}
                            onWheel={(e) => e.currentTarget.blur()}
                            value={state.expiry}
                            onChange={handleInputChange}
                            onFocus={handleInputFocus}
                            onBlur={() =>
                                setState((prev) => ({ ...prev, focus: "" }))
                            }
                        />
                        <input
                            className="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black flex-[0.5]"
                            type="number"
                            name="cvc"
                            placeholder={translations?.cvc}
                            onWheel={(e) => e.currentTarget.blur()}
                            value={state.cvc}
                            onChange={handleInputChange}
                            onFocus={handleInputFocus}
                            onBlur={() =>
                                setState((prev) => ({ ...prev, focus: "" }))
                            }
                        />
                    </div>
                </div>

                <Cards
                    number={state.number}
                    expiry={state.expiry}
                    cvc={state.cvc}
                    name={state.name}
                    focused={state.focus}
                    placeholders={{
                        name: translations?.namePlaceholder,
                    }}
                    locale={{
                        valid: translations?.validPlaceholder,
                    }}
                />
            </div>
        </div>
    );
}

if (document.getElementById("credit-card-form")) {
    createRoot(document.getElementById("credit-card-form")).render(
        <CreditCardForm />
    );
}
