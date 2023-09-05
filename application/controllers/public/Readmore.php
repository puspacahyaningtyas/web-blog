<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CMS Sekolahku | CMS (Content Management System) dan PPDB/PMB Online GRATIS 
 * untuk sekolah SD/Sederajat, SMP/Sederajat, SMA/Sederajat, dan Perguruan Tinggi
 * @version    2.0.0
 * @author     Anton Sofyan | https://facebook.com/antonsofyan | 4ntonsofyan@gmail.com | 0857 5988 8922
 * @copyright  (c) 2014-2017
 * @link       http://sekolahku.web.id
 * @since      Version 2.0.0
 *
 * PERINGATAN :
 * 1. TIDAK DIPERKENANKAN MEMPERJUALBELIKAN APLIKASI INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 2. TIDAK DIPERKENANKAN MENGHAPUS KODE SUMBER APLIKASI.
 * 3. TIDAK MENYERTAKAN LINK KOMERSIL (JASA LAYANAN HOSTING DAN DOMAIN) YANG MENGUNTUNGKAN SEPIHAK.
 */

class Readmore extends Public_Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Readmore
	 */
	public function index() {
		$this->load->helper(['text', 'form']);
		$id = (int) $this->uri->segment(2);
		if ($id && $id != 0 && ctype_digit((string) $id)) {
			$this->load->helper(['captcha', 'string']);
			$this->load->model(['m_posts', 'm_pages', 'm_post_comments']);
			$this->m_posts->increase_viewer($id);
			$this->vars['query'] = $this->model->RowObject('posts', 'id', $id);
			if (filter_var($this->vars['query']->is_deleted, FILTER_VALIDATE_BOOLEAN)) {
				redirect(base_url(), 'refresh');
			}
			$this->vars['page_title'] = $this->vars['query']->post_title;
			$this->vars['post_type'] = 'post';
			if ($this->vars['query']->post_type === 'page') {
				$this->vars['post_type'] = 'page';
			}
			$this->vars['post_author'] = $this->model->RowObject('users', 'id', $this->vars['query']->post_author)->user_full_name;
			$this->vars['content'] = 'themes/'.theme_folder().'/single-post';
			$this->vars['captcha'] = $this->model->set_captcha();
			$this->load->view('themes/'.theme_folder().'/index', $this->vars);
		} else {
			show_404();
		}
	}
}