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

class M_students extends CI_Model {

	/**
	 * Primary key
	 * @var string
	 */
	public static $pk = 'id';

	/**
	 * Table
	 * @var string
	 */
	public static $table = 'students';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get data for pagination
	 * @param string
	 * @param int
	 * @param int
	 * @return Query
	 */
	public function get_where($keyword, $limit = 0, $offset = 0, $sort_field = '', $sort_type = 'ASC') {
		$this->db->select("
			x1.id
			, COALESCE(x1.nis, '') nis
			, COALESCE(x1.nim, '') nim
			, x1.full_name
			, x2.option AS student_status
			, x1.gender
			, COALESCE(x1.birth_place, '') birth_place
			, x1.birth_date
			, x1.photo
			, x1.is_deleted
			");
		$this->db->join('options x2', 'x1.student_status = x2.id', 'LEFT');
		$this->db->where('x1.is_student', 'true');
		$this->db->where('x1.is_alumni', 'false');
		$this->db->group_start();
		$this->db->like('x1.nis', $keyword);
		$this->db->or_like('x2.option', $keyword);
		$this->db->or_like('x1.full_name', $keyword);
		$this->db->or_like('x1.gender', $keyword);
		$this->db->or_like('x1.birth_place', $keyword);
		$this->db->or_like('x1.birth_date', $keyword);
		$this->db->group_end();
		if ($sort_field != '') {
			$this->db->order_by('x1.'.$sort_field, $sort_type);
		}
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}
		return $this->db->get(self::$table. ' x1');
	}

	/**
	 * Get All Data
	 * @return Query
	 */
	public function get_all() {
		return $this->db
			->select('
				x1.id
				, x1.nis
				, x1.nisn
				, x1.full_name
				, x2.option AS student_status
				, x1.gender
				, x1.birth_place
				, x1.birth_date
				, x1.street_address
				, x1.photo
				, x1.is_deleted
				')
			->join('options x2', 'x1.student_status = x2.id', 'LEFT')
			->where('x1.is_student', 'true')
			->where('x1.is_alumni', 'false')
			->get('students x1');
	}

	/**
	 * Get Total row for pagination
	 * @param string
	 * @return int
	 */
	public function total_rows($keyword) {
		return $this->db
			->join('options x2', 'x1.student_status = x2.id', 'LEFT')
			->where('x1.is_student', 'true')
			->where('x1.is_alumni', 'false')
			->group_start()
			->like('x1.nis', $keyword)
			->or_like('x2.option', $keyword)
			->or_like('x1.full_name', $keyword)
			->or_like('x1.gender', $keyword)
			->or_like('x1.birth_place', $keyword)
			->or_like('x1.birth_date', $keyword)
			->group_end()
			->count_all_results('students x1');
	}

	/**
	 * Autocomplete
	 * @param int
	 * @param int
	 * @param string
	 * @return resource
	 */
	public function autocomplete($academic_year_id, $class_group_id, $keyword) {
		$like = '%'.$this->db->escape_like_str($keyword).'%';
		$binding_params = [
			$like,
			$like,
			$like,
			$like,
			intval($academic_year_id),
			intval($class_group_id)
		];
		$query = $this->db->query("
			SELECT x1.id
			  , x1.registration_number
			  , x1.nis
			  , x1.full_name
			  , x1.is_prospective_student
			FROM students x1
			WHERE x1.is_alumni = 'false'
			AND (
				x1.selection_result IS NOT NULL 
				OR x1.selection_result <> 'unapproved'
			)
			AND (
			  x1.registration_number LIKE ?
			  OR x1.nis LIKE ?
			  OR x1.nim LIKE ?
			  OR x1.full_name LIKE ?
			) AND x1.id NOT IN (
			  SELECT student_id FROM class_group_settings
			  WHERE academic_year_id = ?
			  AND class_group_id = ? 
			)
		", $binding_params);
		return $query;
	}

	/**
	 * Get total student by student status
	 */
	public function student_by_student_status() {
		return $this->db->query("
			SELECT x2.`option` AS labels
				, COUNT(*) AS data 
			FROM students x1
			JOIN `options` x2 ON x1.student_status = x2.id
			WHERE x1.is_student = 'true' 
			GROUP BY 1
			ORDER BY 1 ASC
		");
	}
}