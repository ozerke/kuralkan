import React, { useEffect, useState } from "react";

export default function InstallmentsList({
    creditCardNumber,
    price,
    translations,
}) {
    const [installments, setInstallments] = useState([]);
    const [logo, setLogo] = useState();
    const [color, setColor] = useState();

    const cardNumber = creditCardNumber.substring(0, 6);

    const getInstallmentsList = async () => {
        const isBondPayment = document.getElementById("e_bond_no");

        const isCampaignSelected = !!document.querySelector(
            "input[name=campaign]:checked"
        )?.value;

        const campaignInstallments = document.getElementById(
            "campaign-installments"
        )?.value;

        const campaignCode = document.getElementById("campaign-code")?.value;
        const orderId = document.getElementById("order-id")?.value;

        if (cardNumber.length > 5 && !!price) {
            const response = await fetch("/api/get-installments", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    creditCardDigits: cardNumber,
                    price,
                    campaignCode,
                    orderId,
                }),
            })
                .then((resp) => {
                    if (!resp.ok) throw resp;

                    return resp.json();
                })
                .catch((e) => {
                    alert(e.statusText);
                    setInstallments([]);
                    setLogo(undefined);
                    setColor(undefined);
                });

            if (response) {
                if (!!campaignInstallments) {
                    setInstallments(
                        response?.data?.installments.slice(
                            0,
                            campaignInstallments
                        ) ?? []
                    );
                } else {
                    if (isBondPayment || isCampaignSelected) {
                        setInstallments(
                            [response?.data?.installments[0]] ?? []
                        );
                    } else {
                        setInstallments(response?.data?.installments ?? []);
                    }
                }

                setLogo(response?.data?.logo);
                setColor(response?.data?.color);
            }
        } else {
            setInstallments([]);
            setLogo(undefined);
            setColor(undefined);

            return null;
        }
    };

    useEffect(() => {
        getInstallmentsList();
    }, [cardNumber, price]);

    if (!installments || installments.length < 1) return null;

    const style = color
        ? {
              borderColor: color,
              backgroundColor: color,
          }
        : {};

    return (
        <div className="flex flex-col">
            {!!logo && (
                <div className="flex justify-center items-center">
                    <img src={logo} className="h-[40px]" />
                </div>
            )}
            <table className="styled-table">
                <thead
                    className="border-2 text-white text-shadow"
                    style={style}
                >
                    <tr>
                        <th></th>
                        <th className="h-[40px]"></th>
                        <th>{translations.installmentAmount}</th>
                        <th>{translations.totalAmount}</th>
                    </tr>
                </thead>
                <tbody>
                    {installments.length > 1 ? (
                        installments.map((installment, idx) => {
                            return (
                                <tr
                                    className="text-center hover:!bg-gray-300 cursor-pointer transition-all"
                                    key={`installment-${idx}`}
                                >
                                    <td>
                                        <input
                                            type="radio"
                                            name="number_of_installments"
                                            value={installment.months}
                                        />
                                    </td>
                                    <td className="font-medium py-[10px] px-[5px] border-r-[1px] w-[40%]">
                                        {installment.months}{" "}
                                        {translations.installments}
                                    </td>
                                    <td className="font-medium py-[10px] px-[5px] border-r-[1px]">
                                        ₺{installment.perOne}
                                    </td>
                                    <td className="font-medium py-[10px] px-[5px]">
                                        ₺{installment.total}
                                    </td>
                                </tr>
                            );
                        })
                    ) : (
                        <tr className="text-center hover:!bg-gray-300 cursor-pointer transition-all">
                            <td>
                                <input
                                    type="radio"
                                    name="number_of_installments"
                                    value={1}
                                    checked
                                />
                            </td>
                            <td className="font-medium py-[10px] px-[5px] border-r-[1px] w-[40%]">
                                {translations.oneShot}
                            </td>
                            <td className="font-medium py-[10px] px-[5px] border-r-[1px]">
                                ₺{installments[0].perOne}
                            </td>
                            <td className="font-medium py-[10px] px-[5px]">
                                ₺{installments[0].total}
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
}
