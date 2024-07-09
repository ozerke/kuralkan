import React, { useEffect, useRef, useState } from "react";
import { createRoot } from "react-dom/client";
import Gallery from "react-image-gallery";
import MediaItem from "./Item";
import "../../scss/image-gallery.scss";

const mapUrlsToImages = (urls) => {
    return urls.map((url) => {
        return {
            original: url,
            thumbnail: url,
            originalClass: "zoom hover:hidden",
        };
    });
};

const findVariationMedia = (variations, variationId) => {
    const variationData = variations.find(
        (variation) => variation.variation == variationId
    );

    if (variationData) {
        return mapUrlsToImages(variationData.urls);
    }

    return [];
};

export default function ImageGallery() {
    const galleryRef = useRef();
    const [activeVariation, setActiveVariation] = useState(
        document.getElementById("variation-input").value
    );

    const element = document.getElementById("image-gallery-component");
    const variations = JSON.parse(element.getAttribute("data-variations"));

    const [images, setImages] = useState(
        findVariationMedia(variations, activeVariation)
    );

    useEffect(() => {
        document.addEventListener("variation_changed", (variation) =>
            setActiveVariation(variation.detail.variation)
        );

        return () => {
            document.removeEventListener("variation_changed", (variation) =>
                setActiveVariation(variation.detail.variation)
            );
        };
    }, []);

    useEffect(() => {
        setImages(findVariationMedia(variations, activeVariation));
    }, [activeVariation]);

    if (images.length < 1) return null;

    return (
        <div className="relative w-full">
            <Gallery
                ref={galleryRef}
                items={images}
                showPlayButton={false}
                onClick={() => galleryRef?.current.toggleFullScreen()}
                renderItem={(item) => <MediaItem {...item} />}
            />
        </div>
    );
}

if (document.getElementById("image-gallery-component")) {
    createRoot(document.getElementById("image-gallery-component")).render(
        <ImageGallery />
    );
}
