# ajax-images

A plugin for [Question2Answer](http://www.question2answer.org/) that allows to attach images to a question, resizing them on the browser (nice for phones), and uploading them in ajax.

Main features:
* Images are first resized directly on the browser, in order to avoid uploading huge files unnecessarily.
* Each image is then shown in the question, and uploaded in the background to the server using ajax
* Images are attached to the post using metadata
* The images thumbnails are shown in the post page and in the list view
* The images can be enlarged using [Magnific Popup](https://dimsemenov.com/plugins/magnific-popup/)

Todo:
* Option to decide the max size of a file (currently hardcoded)
* Block the form submit button while the image is being uploaded
* Impose a limit to 10 images (upload), 3 images (list view)