<?PHP

use \Carbon\Carbon;

class Vid_competition extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'event_start' => 'required|date',
		'event_end' => 'required|date'
	);

	public function divisions() {
		return $this->hasMany('Vid_division', 'competition_id', 'id');
	}

	public function is_active() {
		if(Carbon::now()->between(new Carbon($this->event_start), new Carbon($this->event_end))) {
			return true;
		}
		return false;
	}
}
