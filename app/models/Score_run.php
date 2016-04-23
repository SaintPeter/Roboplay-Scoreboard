<?PHP

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Score_run extends Eloquent {
	use SoftDeletingTrait;

	protected $guarded = array();

	protected $dates = ['deleted_at'];

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
			// Get time from this event return as a string
			return Carbon::parse($value)->format('g:i a');
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

	public function judge()
	{
		return $this->belongsTo('Judge');
	}
}
