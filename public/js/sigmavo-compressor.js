/**
 * SIGMAVO Universal File Compressor
 * Menangani kompresi gambar (JPG/PNG/WEBP) dan PDF (menggunakan pdf-lib jika diload)
 * secara client-side sebelum dikirim ke server.
 */

const SigmavoCompressor = {
    /**
     * Kompresi 1 File
     */
    async compressSingle(file, maxImgKb = 300, maxPdfKb = 500) {
        if (!file) return file;

        const isImage = file.type.startsWith('image/');
        const isPdf = file.type === 'application/pdf';

        if (isImage) {
            return await this.compressImage(file, maxImgKb);
        } else if (isPdf) {
            return await this.compressPdf(file, maxPdfKb);
        }

        // Jika docx/xlsx, kembalikan file aslinya (tidak dikompres)
        return file;
    },

    /**
     * Kompresi Multiple File (Array/FileList)
     */
    async compressMultiple(files, maxImgKb = 300, maxPdfKb = 500) {
        const compressedFiles = [];
        for (let i = 0; i < files.length; i++) {
            const result = await this.compressSingle(files[i], maxImgKb, maxPdfKb);
            compressedFiles.push(result);
        }
        return compressedFiles;
    },

    // --- ENGINE GAMBAR ---
    compressImage(file, targetKb) {
        return new Promise((resolve) => {
            const targetBytes = targetKb * 1024;
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;

                    const maxDim = 1280;
                    if (width > maxDim || height > maxDim) {
                        if (width > height) {
                            height = Math.round((height * maxDim) / width);
                            width = maxDim;
                        } else {
                            width = Math.round((width * maxDim) / height);
                            height = maxDim;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.85;

                    function attemptCompress() {
                        canvas.toBlob(function(blob) {
                            if (!blob) {
                                resolve(file); return;
                            }
                            if (blob.size > targetBytes && quality > 0.35) {
                                quality -= 0.12;
                                attemptCompress();
                            } else {
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            }
                        }, 'image/jpeg', quality);
                    }
                    attemptCompress();
                };
                img.src = e.target.result;
            };
            reader.onerror = () => resolve(file);
            reader.readAsDataURL(file);
        });
    },

    // --- ENGINE PDF ---
    async compressPdf(file, targetKb) {
        if (typeof PDFLib === 'undefined') return file; // Skip jika library tidak diload
        
        try {
            const arrayBuffer = await file.arrayBuffer();
            const pdfDoc = await PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });

            pdfDoc.setTitle('');
            pdfDoc.setAuthor('');
            pdfDoc.setSubject('');
            pdfDoc.setKeywords([]);
            pdfDoc.setProducer('');
            pdfDoc.setCreator('');

            const pdfBytes = await pdfDoc.save({ useObjectStreams: true });
            return new File([pdfBytes], file.name, {
                type: 'application/pdf',
                lastModified: Date.now()
            });
        } catch (err) {
            return file;
        }
    }
};