<?php
/*
	Ajax Images by Bertrand Gorge, Neayi
	https://neayi.com/

	File: qa-plugin/ajax-images/qa-ai-layer.php
	Description: Displays the images and upload buttons, as well as the JS scripts and CSS

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

class qa_html_theme_layer extends qa_html_theme_base
{
    private $imageNames;
    private $hasImagesOnPage;

    /**
     * Since this is called first, let's initialise our data now
     */
	function doctype() {
        qa_html_theme_base::doctype();

        $this->imageNames = array();
        $this->hasImagesOnPage = false;

        for ($i = 1; $i <= 10; $i++)
            $this->imageNames[] = "attached_image_$i";

        if(isset($this->content['q_view']['raw']['postid']))
        {
            $this->content['q_view']['images'] = $this->getImagesObjectsFor($this->content['q_view']['raw']['postid']);
        }

        if (!empty($this->content['q_list']['qs']))
        {
            foreach ($this->content['q_list']['qs'] as $k => $aQuestion)
                $this->content['q_list']['qs'][$k]['images'] = $this->getImagesObjectsFor($this->content['q_list']['qs'][$k]['raw']['postid']);
        }
    }

    function header()
    {

    }

	function head_script() {
		qa_html_theme_base::head_script();

        if($this->hasImagesOnPage)
        {
            $this->output('<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'magnific-popup/jquery.magnific-popup.min.js"></script>');
            $this->output('<script type="text/javascript">');
            $this->output('$(function(){');
            $this->output('	$(".qa-q-view-ai-list-img, .qa-q-view-ai-form-img").magnificPopup({');
            $this->output('		type: \'image\',');
            $this->output('		tError: \'<a href="%url%">The image</a> could not be loaded.\',');
            $this->output('		image: {');
            $this->output('			titleSrc: \'title\'');
            $this->output('		},');
            $this->output('		gallery: {');
            $this->output('			enabled: true');
            $this->output('		},');
            $this->output('		callbacks: {');
            $this->output('			elementParse: function(item) {');
            $this->output('				console.log(item);');
            $this->output('			}');
            $this->output('		}');
            $this->output('	});');
            $this->output('});');
            $this->output('</script>');
        }
    }

	function head_css() {
		qa_html_theme_base::head_css();

        if($this->hasImagesOnPage)
        {
            $this->output('<link rel="stylesheet" TYPE="text/css" href="'.QA_HTML_THEME_LAYER_URLTOROOT.'magnific-popup/magnific-popup.css"/>');
            $this->output('<link rel="stylesheet" type="text/css" href="'.QA_HTML_THEME_LAYER_URLTOROOT.'css/qa-ai-styles.css" />');
        }
	}

    function body_footer()
    {
        if(isset($this->content['form_q_edit']['fields'])) // check it's a question page
            $this->content['body_footer'] = '<script src="' . qa_html(QA_HTML_THEME_LAYER_URLTOROOT . 'js/image_compress.js') . '" type="text/javascript"></script>';

        qa_html_theme_base::body_footer();
    }

    function main()
    {
        if($this->template == 'ask')
        {
            $toto = $this->content['form'];
        }
        else if(isset($this->content['form_q_edit']['fields']))
        {
            $toto = $this->content['form_q_edit'];

            $this->content['form_q_edit']['fields']['test_tp'] = array();
            $this->content['form_q_edit']['fields']['test_tp']['label'] = 'honk';
            $this->content['form_q_edit']['fields']['test_tp']['type'] = 'custom';
            $this->content['form_q_edit']['fields']['test_tp']['html'] = '<ul id="qa_ai_images_preview">The images will show up here:</ul><input type="file" id="qa-ai-fileupload" multiple>';
            $this->content['form_q_edit']['fields']['test_tp']['note'] = 'honk';

            $titi = $this->content['q_view'];
		}
		qa_html_theme_base::main();
    }
    function q_view_content($q_view)
    {
        $this->output('q_view_content');
        qa_html_theme_base::q_view_content($q_view);
    }

    /**
     * Outputs the content of a question when in a list
     */
	public function q_item_content($q_item)
	{
        qa_html_theme_base::q_item_content($q_item);

        $output = '<ul id="qa_ai_images_preview">';

        foreach($q_item['images'] as $anImage)
        {
            $value = $anImage['filename'];
            if($anImage['isImage'])
            {
                $value = '<img src="'.$anImage['url'].'" alt="'.$anImage['filename'].'" target="_blank"/>';
                $value = '<a href="'.$anImage['url'].'" class="qa-q-view-extra-link qa-q-view-ai-form-img">' . $value . '</a>';
            }
            else // other types of files (PDF, ...)
                $value = '<a href="'.$anImage['url'].'" class="qa-q-view-extra-link">' . $value . '</a>';

            // todo: change all class names!
            $output .= '<li class="qa-q-view-extra-content">'.$value.'</li>';
		}

        $output .= '</ul>';
        $this->output($output);
    }

    function q_view_extra($q_view)
     {
        $this->output('before q_view_extra');
        qa_html_theme_base::q_view_extra($q_view);

        //if(!isset($this->content['form_q_edit']))
		$this->qa_ai_outputImageGallery($q_view);
    }

    /**
     * Add the images that go with the question
     */
    function qa_ai_outputImageGallery(&$q_view)
    {
        $output = '<ul id="qa_ai_images_preview">The images will show up here:';

		$isoutput = false;
        foreach($q_view['images'] as $anImage)
        {
            $value = $anImage['filename'];
            if($anImage['isImage'])
            {
                $value = '<img src="'.$anImage['url'].'" alt="'.$anImage['filename'].'" target="_blank"/>';
                $value = '<a href="'.$anImage['url'].'" class="qa-q-view-extra-link qa-q-view-ai-form-img">' . $value . '</a>';
            }
            else // other types of files (PDF, ...)
                $value = '<a href="'.$anImage['url'].'" class="qa-q-view-extra-link">' . $value . '</a>';

            // todo: change all class names!
            $output .= '<li class="qa-q-view-extra-content">'.$value.'</li>';

            $isoutput = true;
		}

        $output .= '</ul>';
        $this->output($output);

        if($isoutput)
			$this->output('<div style="clear:both;"></div>');
    }

    private function getImagesObjectsFor($postId)
    {
        $images = array();
        require_once QA_INCLUDE_DIR . 'app/blobs.php';

        $blobIds = qa_db_single_select(qa_db_post_meta_selectspec($postId, $this->imageNames));
        foreach($blobIds as $blobId)
        {
            if (qa_blob_exists($blobId))
            {
                $blob = qa_read_blob($blobId);
                $bloburl = qa_get_blob_url($blobId);
                $filename = $blob['filename'];

                $format = $blob['format'];
                $bIsImage = ($format == 'jpg' || $format == 'jpeg' || $format == 'png' || $format == 'gif');

                if ($bIsImage)
                    $bloburl = str_replace('qa=blob', 'qa=image', $bloburl);

                $images[] = array('blobId' => $blobId,
                                  'url' => $bloburl,
                                  'isImage' => $bIsImage,
                                  'filename' => $filename);

                $this->hasImagesOnPage = true;
            }
            else
            {
                // non existant $blobId ?
                throw new Exception('Non existant blobId ' .  $blobId);

                // todo: Alternatively, we could stay silent and just delete this value
            }
        }

        return $images;
    }

}