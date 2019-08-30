<?php
/*
	Ajax Images by Bertrand Gorge, Neayi
	https://neayi.com/

	File: qa-plugin/ajax-images/qa-ai-process.php
	Description: Interrups the Ajax requests in order to catch image uploads

	MIT License

	Copyright (c) 2019 neayi

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.

*/

class qa_ai_process {

	function init_ajax()
	{
        $upload_image = qa_post_text('upload_image');

        if ($upload_image == 'true')
        {
            $filename = qa_post_text('filename');

            require_once QA_INCLUDE_DIR . 'app/cookies.php';
            require_once QA_INCLUDE_DIR . 'app/upload.php';
			require_once QA_INCLUDE_DIR . 'app/options.php';
			require_once QA_INCLUDE_DIR . 'db/selects.php';

        	$file = reset($_FILES);
            $result = qa_upload_file($file['tmp_name'], $filename);

            if(isset($result['error']))
            {
                echo "QA_AJAX_RESPONSE\n0\nCould not upload the file: ".$result['error']."\n";
            }
            else
            {
				// Success:
				echo "QA_AJAX_RESPONSE\n1\n";

                // The blobid allows to show the image with the URL : http://...question2answer/?qa=blob&qa_blobid=6748342318866642209
                echo $result['blobid'] . "\n";
            }
		}

        $delete_image = qa_post_text('delete_image');

        if (!empty($delete_image))
        {
			require_once QA_INCLUDE_DIR . 'app/blobs.php';
			qa_delete_blob($delete_image); // does not return anything

			echo "QA_AJAX_RESPONSE\n1";
        }
    }
}
