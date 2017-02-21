<!-- partial.showvideo -->
<div class="row">
    <h4>
        @if(count($video->awards))
            <img src="{{ asset('images/star.png') }}">
        @endif
       {{ $video->name }}
    </h4>
    @if($show_division)
        <h5>{{ $video->vid_division->name }}</h5>
    @endif
    @if(count($video->awards))
        @foreach($video->awards as $award)
            Winner: {{ $award->name }}<br>
        @endforeach
        <br>
    @endif
    <div class="embed-responsive embed-responsive-16by9">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" style="border: 1px solid black" id="ytplayer" type="text/html" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}" frameborder="0"></iframe>
        </div>
    </div>
</div>