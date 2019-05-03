@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {{--<reprodutor></reprodutor>--}}
                <filas></filas>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        window.onSpotifyWebPlaybackSDKReady = () => {
            const token = 'BQDJN2WWfNAdvXl6J9WYFo6trBkwbHCWwpZmo6_mkq5BGvKrnXls_IBIbd9P5wSr87o7u3pYAGOQmWo7sddA2P2fGKcBilE9QybnWC7eOGqPoASIjvZw3YGd2liFJ8zZ34lkO6tqAZOjEVJTihcxSIx2KV9hTviPeBzE1kPiz8wUHsHGZXmXk4HpUg';
            const player = new Spotify.Player({
                name: 'Ouvir Juntos!',
                getOAuthToken: cb => { cb(token); }
            });

            // Error handling
            player.addListener('initialization_error', ({ message }) => { console.error(message); });
            player.addListener('authentication_error', ({ message }) => { console.error(message); });
            player.addListener('account_error', ({ message }) => { console.error(message); });
            player.addListener('playback_error', ({ message }) => { console.error(message); });

            // Playback status updates
            player.addListener('player_state_changed', state => { console.log(state); });

            // Ready
            player.addListener('ready', ({ device_id }) => {
                console.log('Ready with Device ID', device_id);
            });

            // Not Ready
            player.addListener('not_ready', ({ device_id }) => {
                console.log('Device ID has gone offline', device_id);
            });

            // Connect to the player!
            player.connect();
        };
    </script>
@endsection
