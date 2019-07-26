<?php
/*
	Ajax Images by Bertrand Gorge, Neayi
	https://neayi.com/

	File: qa-plugin/ajax-images/qa-ai-event.php
	Description: Store the blobIds in the DB

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

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

require_once QA_INCLUDE_DIR.'qa-db-metas.php';

class qa_tp_event
{
    function process_event ($event, $userid, $handle, $cookieid, $params)
    {
        $image_names = array();
        for ($i = 1; $i <= 10; $i++)
            $image_names[] = "attached_image_$i";

        switch ($event)
        {
            case 'q_queue':
            case 'q_post':
            case 'q_edit':
                $existing_images = qa_db_single_select(qa_db_post_meta_selectspec($params['postid'], $image_names));
                $removed_images = array();

                // remove all meta first (not very efficient but at least allows to keep the images in a sequential order)
                qa_db_postmeta_clear($params['postid'], $image_names);

                foreach ($GLOBALS['UploadedBlobIds'] as $k => $BlobId)
                {
                    qa_db_postmeta_set($params['postid'], $image_names[$k], $BlobId);
                }

                require_once QA_INCLUDE_DIR . 'app/blobs.php';

                foreach ($existing_images as $existingBlobId)
                {
                    if (!in_array($existingBlobId, $GLOBALS['UploadedBlobIds']))
                    {
                        qa_delete_blob($existingBlobId);
                    }
                }


                break;
            case 'q_delete':
                // Todo : Delete the blobs

                // Then clear the DB
                qa_db_postmeta_clear($params['postid'], $image_names);
                break;
        }
	}
}
