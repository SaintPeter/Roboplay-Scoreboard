<?PHP

use \Carbon\Carbon;

class Vid_competition extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'event_start' => 'required|date',
		'event_end' => 'required|date'
	);

	public function getDates()
    {
        return array('created_at', 'updated_at', 'event_start', 'event_end');
    }

	public function divisions() {
		return $this->hasMany('Vid_division', 'competition_id', 'id');
	}

	public function comp_year() {
		return $this->morphToMany('CompYear', 'yearable');
	}

	public function is_active() {
		if(Carbon::now()->between($this->event_start, $this->event_end)) {
			return true;
		}
		return false;
	}
}
