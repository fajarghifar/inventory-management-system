function previewImage() {
    const image = document.querySelector("#image");
    const imagePreview = document.querySelector("#image-preview");

    const oFReader = new FileReader();
    oFReader.readAsDataURL(image.files[0]);

    oFReader.onload = function (oFREvent) {
        imagePreview.src = oFREvent.target.result;
    };
}
