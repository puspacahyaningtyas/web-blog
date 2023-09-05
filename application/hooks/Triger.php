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

class Triger {
	
	/**
     * The CodeIgniter super object
     *
     * @var object
     * @access private
     */
    private $CI;

	/**
     * Class constructor
     */
    public function __construct() {
		$this->CI = &get_instance();
	}

	/**
     * Set Session Here
     */
	public function index() {
		$this->CI->load->model(['m_settings', 'm_themes']);
		$settings = [];
		if (! $this->CI->auth->is_logged_in()) {
			$settings = $this->CI->m_settings->get_setting_values('public');
		} else {
			$settings = $this->CI->m_settings->get_setting_values($this->CI->session->userdata('user_type'));
		}
		if (count($settings) > 0) {
			$session_data = [];
			foreach($settings as $key => $value) {
				if ($key == 'school_level') {
					$school_level = [1,2,3,4,5];
					if (!in_array($value, $school_level)) {
						$options = $this->CI->model->RowObject('options', 'id', $value);
						$session_data[$key] = substr($options->option, 0, 1); // ex : 1 - SD / Sekolah Dasar <-- ambil digit pertama sebagai kode jenjang sekolah
					} else {
						$session_data[$key] = $value;	
					}						
				} else {
					$session_data[$key] = $value;	
				}
			}
		}

		// Set Active Theme
		$session_data['theme'] = $this->CI->m_themes->get_active_themes();
		
		// Set Session Data
		$this->CI->session->set_userdata($session_data);
	}
}