/**
 * Compress uploaded images so that they are not too large
 *
 * See: https://zocada.com/compress-resize-images-javascript-browser/
 */

/*
<!-- HTML Part -->
<input id="file" type="file" accept="image/*">
<script>
    document.getElementById("upload_file_1").addEventListener("change", function (event) {
	compress(event);
});
</script>
*/


function compress(e)
{
    const max_size = 600;
    var nFiles = e.target.files.length;

    for (var nFileId = 0; nFileId < nFiles; nFileId++)
    {
        const fileName = e.target.files[nFileId].name;
        const originalsize = e.target.files[nFileId].size;

        const reader = new FileReader();
        reader.readAsDataURL(e.target.files[nFileId]);
        reader.onload = event => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                    const elem = document.createElement('canvas');
                    var width = 0;
                    var height = 0;

                    if (img.width > img.height)
                    {
                        width = max_size;
                        const scaleFactor = width / img.width;
                        height = img.height * scaleFactor;
                    }
                    else
                    {
                        height = max_size;
                        const scaleFactor = height / img.height;
                        width = img.width * scaleFactor;
                    }

                    elem.width = width;
                    elem.height = height;
                    const ctx = elem.getContext('2d');
                    // img.width and img.height will contain the original dimensions
                    ctx.drawImage(img, 0, 0, width, height);
                    ctx.canvas.toBlob((blob) => {
                        const file = new File([blob], fileName, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });

                        var img = document.createElement("img");
                        img.file = file;
                        var preview = document.getElementById('qa_ai_images_preview');

                        // Now show a miniature in the div:
                        var li = document.createElement("li");
                        preview.appendChild(li);

                        li.appendChild(img);

                        var reader = new FileReader();
                        reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
                        reader.readAsDataURL(file);

                        var info = document.createElement("span");

                        info.innerHTML = "0%";
                        li.appendChild(info);

                        // Now start uploading the file:
                        new FileUpload(info, img.file, fileName);

                    }, 'image/jpeg', 1);
                },
                reader.onerror = error => console.log(error);
        };
    }
}

/**
 * FileUpload : uploads a file to the server and store it as a blob. When finished,
 * the blobId is stored in a hidden input
 *
 * @param {*} info a DOM element which we will update with the upload progress
 * @param {*} file a File object that we will upload
 * @param {*} fileName the original filename of the file
 *
 * @see https://developer.mozilla.org/fr/docs/Web/API/File/Using_files_from_web_applications
 *
 */
function FileUpload(info, file, fileName)
{
    qa_show_waiting_after(info, false);

    // Disable the save button:
    $('.qa-form-tall-button-save').attr("disabled", true);

    var xhr = new XMLHttpRequest();

    // Allows for a progress bar to be updated
    xhr.upload.addEventListener("progress", function(e) {
            if (e.lengthComputable)
            {
                var percentage = Math.round((e.loaded * 100) / e.total);
                info.innerHTML = percentage + "%";
            }
          }, false);

    // When the upload is complete, hide the progress indicator (Throbber)
    xhr.upload.addEventListener("load", function(e){
            info.innerHTML = ""; // 100%
            qa_hide_waiting(info);
        }, false);

    // When the ajax request is completed, we can parse the data sent back
    // with the ID of the new image.
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4)
        {
            var header = 'QA_AJAX_RESPONSE';
            var headerpos = xhr.response.indexOf(header);

            if (headerpos >= 0)
            {
                var lines = xhr.response.substr(headerpos + header.length).replace(/^\s+/, '').split("\n");
                if (lines[0] == '1')
                {
                    //  Create a DOM hidden input that we will use to store the blobId

                    var hiddenBlobIDInput = document.createElement("input");
                    hiddenBlobIDInput.setAttribute('type', 'hidden');
                    hiddenBlobIDInput.name = 'blobId[]';
                    hiddenBlobIDInput.value = "0";
                    info.parentNode.appendChild(hiddenBlobIDInput);

                    var BlobId = lines[1];
                    hiddenBlobIDInput.value = BlobId;
                }
                else if (lines[0] == '0')
                {
                    alert(lines[1]);
                    info.parentNode.remove();
                }

                $('.qa-form-tall-button-save').removeAttr("disabled");
            }
            else
                qa_ajax_error();
        }
    }

    // Prepare the ajax request, using POST
    xhr.open("POST", "index.php");

    // Build a form data object in order to store the values in different mime parts
    var formData = new FormData();

    // qa == ajax --> route to the ajax handler in index.php
    formData.append("qa", "ajax");

    // This will tell our specific code (in qa-tp_process.php to handle the image upload)
    formData.append("upload_image", 'true');

    // Give the original filename
    formData.append("filename", fileName);

    // Finaly add the image itself
    formData.append("new_image", file);

    // Now that our form data is complete, send it through:
    xhr.send(formData);
}


// Attach an event listener to our File input, in order to start compressing and uploading files:
document.getElementById("qa-ai-fileupload").addEventListener("change", function (event) {
    compress(event);
});

function removeAjaxImage(button, blobId)
{
    qa_show_waiting_after(button, false);

    var xhr = new XMLHttpRequest();

    // When the ajax request is completed, we can parse the data sent back
    // with the ID of the new image.
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4)
        {
            var header = 'QA_AJAX_RESPONSE';
            var headerpos = xhr.response.indexOf(header);

            if (headerpos >= 0)
            {
                var lines = xhr.response.substr(headerpos + header.length).replace(/^\s+/, '').split("\n");
                if (lines[0] == '1')
                {
                    qa_hide_waiting(button);
                    button.parentNode.remove();
                }
                else if (lines[0] == '0')
                    alert(lines[1]);
            }
            else
                qa_ajax_error();
        }
    }

    // Prepare the ajax request, using POST
    xhr.open("POST", "index.php");

    // Build a form data object in order to store the values in different mime parts
    var formData = new FormData();

    // qa == ajax --> route to the ajax handler in index.php
    formData.append("qa", "ajax");

    // This will tell our specific code (in qa-tp_process.php to handle the image upload)
    formData.append("delete_image", blobId);

    // Now that our form data is complete, send it through:
    xhr.send(formData);

    return false;
}
