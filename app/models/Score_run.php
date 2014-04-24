<?PHP

use Carbon\Carbon;

class Score_run extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'run_number' => 'required',
		'run_time' => 'required',
		'scores' => 'required',
		'total' => 'required',
		'judge_id' => 'required',
		'team_id' => 'required',
		'challenge_id' => 'required',
		'division_id' => 'required'
	);

	/* Mutators and Assignors
	   ------------------------------ */
	public function getScoresAttribute($value) {
		return unserialize($value);
	}

	public function setScoresAttribute($value) {
		$this->attributes['scores'] = serialize($value);
	}

	// Note:  run_time = RunTime
	public function getRunTimeAttribute($value) {
		if(isset($value)) {
			// Get time from this event, change it to local time, return as a string
			$dt = new Carbon($value, new DateTimeZone("UTC"));
			$dt->setTimeZone("PST");
			return $dt->format('g:i a');
		} else {
			return "Time Error";
		}
	}

	/* Relationships
	   ------------------------------ */
	public function teams()
	{
		return $this->hasMany('Team');
	}

	public function challenges()
	{
		return $this->hasMany('Challenge');
	}

	public function divisions()
	{
		return $this->hasMany('Division');
	}
}
