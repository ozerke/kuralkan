import React from "react";
import Magnifier from "./Magnifier";

const defaultProps = {
    description: "",
    fullscreen: "",
    isFullscreen: false,
    originalAlt: "",
    originalHeight: "",
    originalWidth: "",
    originalTitle: "",
    sizes: "",
    srcSet: "",
    loading: "eager",
};

const Item = React.memo((props) => {
    const {
        description,
        fullscreen,
        handleImageLoaded,
        isFullscreen,
        onImageError,
        original,
        originalAlt,
        originalHeight,
        originalWidth,
        originalTitle,
        sizes,
        srcSet,
        loading,
    } = { ...defaultProps, ...props };

    const itemSrc = isFullscreen ? fullscreen ?? original : original;

    const ImageItem = React.forwardRef((props, ref) => (
        <img
            ref={ref}
            className="image-gallery-image"
            {...props}
            src={itemSrc}
            alt={originalAlt}
            srcSet={srcSet}
            height={originalHeight}
            width={originalWidth}
            sizes={sizes}
            title={originalTitle}
            onLoad={(event) =>
                handleImageLoaded ? handleImageLoaded(event, original) : null
            }
            onError={onImageError}
            loading={loading}
        />
    ));

    return (
        <div>
            <Magnifier
                key={itemSrc}
                src={itemSrc}
                height={originalHeight}
                width={originalWidth}
                Item={ImageItem}
            />
            {description && (
                <span className="image-gallery-description">{description}</span>
            )}
        </div>
    );
});

Item.displayName = "Item";

export default Item;
