import imageCompression from "browser-image-compression";

const input = document.getElementById("image");
const info = document.getElementById("image-info");
const preview = document.getElementById("image-preview");

function mb(bytes) {
    return bytes / (1024 * 1024);
}

if (input && info && preview) {
    input.addEventListener("change", async (e) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const originalMB = mb(file.size);
        info.textContent = `Geselecteerd: ${originalMB.toFixed(2)} MB`;

        preview.src = URL.createObjectURL(file);
        preview.classList.remove("hidden");

        if (originalMB <= 7.5) {
            info.textContent += " — geen compressie nodig.";
            return;
        }

        info.textContent = `Compressie bezig… (${originalMB.toFixed(2)} MB)`;

        const compressed = await imageCompression(file, {
            maxSizeMB: 2,
            maxWidthOrHeight: 2048,
            useWebWorker: true,
            initialQuality: 0.8,
        });

        const newFile = new File([compressed], file.name, { type: compressed.type });

        const dt = new DataTransfer();
        dt.items.add(newFile);
        input.files = dt.files;

        const newMB = mb(newFile.size);
        info.textContent = `Gecomprimeerd: ${originalMB.toFixed(2)} MB → ${newMB.toFixed(2)} MB`;

        preview.src = URL.createObjectURL(newFile);
    });
}
