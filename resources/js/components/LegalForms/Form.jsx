import React, { useCallback, useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { createRoot } from "react-dom/client";
import IdCardImage from "./images/id_card.jpeg";
import NewIdCardImage from "./images/new_id_card.jpeg";
import TempIdDocImage from "./images/temp_id_doc.jpeg";

function NationalIdAlert({ nationalId, text }) {
    return (
        <div
            className="bg-blue-500 border-t-4 border-blue-600 rounded-b text-white px-4 py-3 shadow-md rounded-md"
            role="alert"
        >
            <div class="flex">
                <div>
                    <svg
                        class="fill-current h-6 w-6 text-teal-500 mr-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">
                        {text}: {nationalId}
                    </p>
                </div>
            </div>
        </div>
    );
}

function valueExistsInObject(object, str, exceptions = []) {
    for (const key in object) {
        if (exceptions.includes(key)) continue;

        if (object[key] === str) {
            return true;
        }
    }
    return false;
}

export default function Form() {
    const [usageType, setUsageType] = useState("individual");
    const [typeOfId, setTypeOfId] = useState("id_card");

    const [nationalId, setNationalId] = useState();

    const [translations, setTranslations] = useState();
    const [artesFields, setArtesFields] = useState();

    const { register, setValue, getValues } = useForm();

    const resolveImageByType = useCallback(() => {
        switch (typeOfId) {
            case "id_card":
                return IdCardImage;
            case "new_id_card":
                return NewIdCardImage;
            case "temp_id_doc":
                return TempIdDocImage;
            default:
                return null;
        }
    }, [typeOfId]);

    const validateForm = () => {
        const values = getValues();

        const nationalIdExists = valueExistsInObject(values, nationalId, [
            "authorized_national_id",
        ]);

        if (nationalIdExists) {
            alert(translations.nationalIdError);
        } else {
            submitArtesForm();
        }
    };

    useEffect(() => {
        setTranslations(
            JSON.parse(
                document
                    .getElementById("legal-registration-form")
                    .getAttribute("data-translations")
            )
        );

        setArtesFields(
            JSON.parse(
                document
                    .getElementById("legal-registration-form")
                    .getAttribute("data-artesFields")
            )
        );

        setNationalId(
            document
                .getElementById("legal-registration-form")
                .getAttribute("data-nationalId")
        );
    }, []);

    useEffect(() => {
        if (artesFields) {
            if (artesFields?.usage_type) {
                setUsageType(artesFields.usage_type);
            }

            if (artesFields?.insurance_number) {
                setValue("insurance_number", artesFields.insurance_number);
            }

            if (
                artesFields?.usage_type &&
                artesFields.usage_type === "individual"
            ) {
                if (artesFields?.private?.id_type) {
                    setTypeOfId(artesFields.private.id_type);
                }

                setValue("id_serial", artesFields?.private?.id_serial);
                setValue("id_no", artesFields?.private?.id_no);
                setValue(
                    "new_id_serial_no",
                    artesFields?.private?.new_id_serial_no
                );
                setValue("temp_doc_no", artesFields?.private?.temp_doc_no);
            }

            if (
                artesFields?.usage_type &&
                artesFields.usage_type === "commercial"
            ) {
                if (artesFields?.commercial?.id_type) {
                    setTypeOfId(artesFields.commercial.id_type);
                }

                setValue("id_serial", artesFields?.commercial?.id_serial);
                setValue("id_no", artesFields?.commercial?.id_no);
                setValue("new_id_serial_no", artesFields?.private?.commercial);
                setValue("temp_doc_no", artesFields?.commercial?.temp_doc_no);

                setValue("street", artesFields?.commercial?.street);
                setValue(
                    "neighbourhood",
                    artesFields?.commercial?.neighbourhood
                );
                setValue("building_no", artesFields?.commercial?.building_no);
                setValue("flat_no", artesFields?.commercial?.flat_no);
                setValue(
                    "authorized_name",
                    artesFields?.commercial?.authorized_name
                );
                setValue(
                    "authorized_national_id",
                    artesFields?.commercial?.authorized_national_id
                );
                setValue(
                    "representation_type",
                    artesFields?.commercial?.representation_type
                );
                setValue(
                    "number_of_documents",
                    artesFields?.commercial?.number_of_documents
                );
                setValue(
                    "place_of_retrieval",
                    artesFields?.commercial?.place_of_retrieval
                );
                setValue(
                    "document_date",
                    artesFields?.commercial?.document_date
                );
                setValue("end_date", artesFields?.commercial?.end_date);
            }
        }
    }, [artesFields]);

    const Individual = () => {
        return (
            <>
                <NationalIdAlert
                    nationalId={nationalId}
                    text={translations.yourNationalId}
                />
                <fieldset>
                    <legend className="text-lg font-bold leading-6 text-gray-900 mb-4">
                        {translations.typeOfTheDocument}
                    </legend>
                    <div className="flex flex-col gap-2">
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setTypeOfId(e.target.value)}
                                checked={typeOfId === "id_card"}
                                value="id_card"
                                name="id_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.idCard}
                            </label>
                        </div>
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setTypeOfId(e.target.value)}
                                checked={typeOfId === "new_id_card"}
                                value="new_id_card"
                                name="id_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.newIdCard}
                            </label>
                        </div>
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setTypeOfId(e.target.value)}
                                checked={typeOfId === "temp_id_doc"}
                                value="temp_id_doc"
                                name="id_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.temporaryIdDoc}
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    {typeOfId === "id_card" && (
                        <div className="flex flex-col gap-2">
                            <div className="flex flex-col gap-3 w-full">
                                <label
                                    htmlFor="id_serial"
                                    className="block text-lg font-semibold leading-6 text-gray-900"
                                >
                                    {translations.serial}
                                </label>
                                <input
                                    {...register("id_serial")}
                                    id="id_serial"
                                    type="text"
                                    className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                                />
                            </div>
                            <div className="flex flex-col gap-3 w-full">
                                <label
                                    htmlFor="id_no"
                                    className="block text-lg font-semibold leading-6 text-gray-900"
                                >
                                    {translations.no}
                                </label>
                                <input
                                    {...register("id_no")}
                                    id="id_no"
                                    type="text"
                                    className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                                />
                            </div>
                        </div>
                    )}
                    {typeOfId === "new_id_card" && (
                        <div className="flex flex-col gap-3 w-full">
                            <label
                                htmlFor="new_id_serial_no"
                                className="block text-lg font-semibold leading-6 text-gray-900"
                            >
                                {translations.serialNo}
                            </label>
                            <input
                                {...register("new_id_serial_no")}
                                id="new_id_serial_no"
                                type="text"
                                maxLength={9}
                                className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                            />
                        </div>
                    )}

                    {typeOfId === "temp_id_doc" && (
                        <div className="flex flex-col gap-3 w-full">
                            <label
                                htmlFor="temp_doc_no"
                                className="block text-lg font-semibold leading-6 text-gray-900"
                            >
                                {translations.documentNo}
                            </label>
                            <input
                                {...register("temp_doc_no")}
                                id="temp_doc_no"
                                type="text"
                                className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                            />
                        </div>
                    )}
                </fieldset>
                <div className="flex w-full items-center justify-center">
                    <img src={resolveImageByType()} className="w-full h-auto" />
                </div>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="id_doc_file_front"
                        >
                            {translations.uploadIdDocFront}
                        </label>
                        <input
                            id="id_doc_file_front"
                            type="file"
                            name="id_doc_file_front"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="id_doc_file_back"
                        >
                            {translations.uploadIdDocBack}
                        </label>
                        <input
                            id="id_doc_file_back"
                            type="file"
                            name="id_doc_file_back"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
            </>
        );
    };

    const Commercial = () => {
        return (
            <>
                <div className="h-2 bg-black w-full rounded-md" />
                <h3 className="text-2xl font-bold uppercase">
                    {translations.addressDetails}
                </h3>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="street"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.street}
                        </label>
                        <input
                            {...register("street")}
                            id="street"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="neighbourhood"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.neighbourhood}
                        </label>
                        <input
                            {...register("neighbourhood")}
                            id="neighbourhood"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="building_no"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.buildingNo}
                        </label>
                        <input
                            {...register("building_no")}
                            id="building_no"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="flat_no"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.flatNo}
                        </label>
                        <input
                            {...register("flat_no")}
                            id="flat_no"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>

                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="authorized_name"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.authorizedName}
                        </label>
                        <input
                            {...register("authorized_name")}
                            id="authorized_name"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="authorized_national_id"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.authorizedNationalId}
                        </label>
                        <input
                            {...register("authorized_national_id")}
                            id="authorized_national_id"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>

                <NationalIdAlert
                    nationalId={nationalId}
                    text={translations.yourNationalId}
                />

                <fieldset>
                    <legend className="text-lg font-bold leading-6 text-gray-900 mb-4">
                        {translations.typeOfTheDocument}
                    </legend>
                    <div className="flex flex-col gap-2">
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setTypeOfId(e.target.value)}
                                checked={typeOfId === "id_card"}
                                value="id_card"
                                name="id_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.idCard}
                            </label>
                        </div>
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setTypeOfId(e.target.value)}
                                checked={typeOfId === "new_id_card"}
                                value="new_id_card"
                                name="id_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.newIdCard}
                            </label>
                        </div>
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setTypeOfId(e.target.value)}
                                checked={typeOfId === "temp_id_doc"}
                                value="temp_id_doc"
                                name="id_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.temporaryIdDoc}
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    {typeOfId === "id_card" && (
                        <div className="flex flex-col gap-2">
                            <div className="flex flex-col gap-3 w-full">
                                <label
                                    htmlFor="id_serial"
                                    className="block text-lg font-semibold leading-6 text-gray-900"
                                >
                                    {translations.serial}
                                </label>
                                <input
                                    {...register("id_serial")}
                                    name="id_serial"
                                    id="id_serial"
                                    type="text"
                                    className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                                />
                            </div>
                            <div className="flex flex-col gap-3 w-full">
                                <label
                                    htmlFor="id_no"
                                    className="block text-lg font-semibold leading-6 text-gray-900"
                                >
                                    {translations.no}
                                </label>
                                <input
                                    {...register("id_no")}
                                    id="id_no"
                                    type="text"
                                    className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                                />
                            </div>
                        </div>
                    )}
                    {typeOfId === "new_id_card" && (
                        <div className="flex flex-col gap-3 w-full">
                            <label
                                htmlFor="new_id_serial_no"
                                className="block text-lg font-semibold leading-6 text-gray-900"
                            >
                                {translations.serialNo}
                            </label>
                            <input
                                {...register("new_id_serial_no")}
                                id="new_id_serial_no"
                                type="text"
                                className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                            />
                        </div>
                    )}

                    {typeOfId === "temp_id_doc" && (
                        <div className="flex flex-col gap-3 w-full">
                            <label
                                htmlFor="temp_doc_no"
                                className="block text-lg font-semibold leading-6 text-gray-900"
                            >
                                {translations.documentNo}
                            </label>
                            <input
                                {...register("temp_doc_no")}
                                id="temp_doc_no"
                                type="text"
                                className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                            />
                        </div>
                    )}
                </fieldset>
                <div className="flex w-full items-center justify-center">
                    <img src={resolveImageByType()} className="w-full h-auto" />
                </div>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="representation_type"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.representationType}
                        </label>
                        <select
                            {...register("representation_type")}
                            id="representation_type"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        >
                            <option selected="selected" value="" disabled>
                                {translations.selectRepresentationType}
                            </option>
                            <option value="1">
                                {translations.unspecified}
                            </option>
                            <option value="2">{translations.proxy}</option>
                            <option value="3">{translations.official}</option>
                            <option value="4">{translations.witness}</option>
                            <option value="5">
                                {translations.interpreter}
                            </option>
                            <option value="6">{translations.parent}</option>
                            <option value="7">{translations.guardian}</option>
                            <option value="8">{translations.trustee}</option>
                            <option value="9">
                                {translations.representative}
                            </option>
                            <option value="10">
                                {translations.legalRepresentative}
                            </option>
                            <option value="11">
                                {translations.estateRepresentative}
                            </option>
                        </select>
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="number_of_documents"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.numberOfDocuments}
                        </label>
                        <input
                            {...register("number_of_documents")}
                            id="number_of_documents"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="place_of_retrieval"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.placeOfRetrieval}
                        </label>
                        <input
                            {...register("place_of_retrieval")}
                            id="place_of_retrieval"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="document_date"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.documentDate}
                        </label>
                        <input
                            {...register("document_date")}
                            id="document_date"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="end_date"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.endDate}
                        </label>
                        <input
                            {...register("end_date")}
                            id="end_date"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                <div className="h-2 bg-black w-full rounded-md" />
                <h3 className="text-2xl font-bold uppercase">
                    {translations.documentsYouNeedToAdd}
                </h3>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="signature_circular"
                        >
                            {translations.signatureCircular}
                        </label>
                        <input
                            id="signature_circular"
                            name="signature_circular"
                            type="file"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="operating_certificate"
                        >
                            {translations.operatingCertificate}
                        </label>
                        <input
                            id="operating_certificate"
                            name="operating_certificate"
                            type="file"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="commercial_registry_gazette"
                        >
                            {translations.commercialRegistryGazette}
                        </label>
                        <input
                            id="commercial_registry_gazette"
                            name="commercial_registry_gazette"
                            type="file"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="official_identity_front"
                        >
                            {translations.officialIdentityFrontSide}
                        </label>
                        <input
                            id="official_identity"
                            name="official_identity_front"
                            type="file"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="official_identity_back"
                        >
                            {translations.officialIdentityBackSide}
                        </label>
                        <input
                            id="official_identity"
                            name="official_identity_back"
                            type="file"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            className="block text-lg font-semibold leading-6 text-gray-900"
                            htmlFor="power_of_attorney"
                        >
                            {translations.uploadPowerAttorney}
                        </label>
                        <input
                            id="power_of_attorney"
                            name="power_of_attorney"
                            type="file"
                            className="text-md text-stone-500 file:mr-5 file:py-1 file:px-3 file:border-[1px] file:text-xs file:font-medium file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700"
                        />
                    </div>
                </fieldset>
            </>
        );
    };

    if (!translations) return null;

    return (
        <>
            <div className="flex flex-col gap-8">
                <fieldset>
                    <legend className="text-lg font-bold leading-6 text-gray-900 mb-4">
                        {translations.usageType}
                    </legend>
                    <div className="flex flex-col gap-2">
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setUsageType(e.target.value)}
                                value="individual"
                                checked={usageType === "individual"}
                                name="usage_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.private}
                            </label>
                        </div>
                        <div className="flex items-center gap-3">
                            <input
                                onChange={(e) => setUsageType(e.target.value)}
                                checked={usageType === "commercial"}
                                value="commercial"
                                name="usage_type"
                                type="radio"
                                className="h-6 w-6 border-gray-300 text-blue-600 focus:ring-blue-600"
                            />
                            <label className="block text-md font-semibold leading-6 text-gray-900">
                                {translations.commercial}
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div className="flex flex-col gap-3 w-full">
                        <label
                            htmlFor="insurance_number"
                            className="block text-lg font-semibold leading-6 text-gray-900"
                        >
                            {translations.insurancePolicyNumber}
                        </label>
                        <input
                            {...register("insurance_number")}
                            id="insurance_number"
                            type="text"
                            className="flex-auto border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black"
                        />
                    </div>
                </fieldset>
                {usageType === "individual" ? <Individual /> : <Commercial />}
                <button
                    type="button"
                    onClick={validateForm}
                    className="font-bold rounded-md text-center leading-[50px] h-[50px] hover:text-white text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors trigger-disable"
                >
                    {translations.submit}
                </button>
            </div>
        </>
    );
}

if (document.getElementById("legal-registration-form")) {
    createRoot(document.getElementById("legal-registration-form")).render(
        <Form />
    );
}
