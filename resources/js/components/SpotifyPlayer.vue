<template>
    <div class="container">
        <vue-topprogress ref="topProgress"></vue-topprogress>

        <div v-if="musicaAtual.status" class="row justify-content-center">
            <div class="col-md-8 col-sm-12">
                <div>
                    <div class="card card-player shadow">
                        <div class="card-body text-white">
                            <img class="img-fluid rounded float-left mr-3 item-image" height="25%" width="25%" :src="musicaAtual.image" alt="Bologna">
                            <h4 class="card-title">{{ musicaAtual.data }}</h4>
                            <div class="float-right">
                                <small class="text-muted">Playlist: {{ musicaAtual.nome_fila}}</small>
                            </div>
                            <small class="text-muted cat">
                                <span class="d-block p-0"><i class="fas fa-user-alt text-secondary"></i>  Por {{ musicaAtual.autor }}</span>
                                <span class="d-block p-0"><i class="fas fa-play text-secondary"></i>  {{ musicaAtual.plays }} plays</span>
                                <span class="d-block p-0"><i class="fas fa-heart text-secondary"></i>  {{ musicaAtual.likes }} favs</span>
                            </small>
                            <div class="text-center align-middle">
                                <span v-if="!musicaAtual.liked" v-on:click="likeMusica" class="float-right align-middle"><i class="fas fa-heart fa-3x text-secondary"></i></span>
                                <span v-if="musicaAtual.liked" v-on:click="likeMusica" class="float-right align-middle"><i class="fas fa-heart fa-3x text-danger"></i></span>
                                <i v-if="situacaoPlayer === 'pause' && conectado" v-on:click="play" class="fas fa-play-circle fa-3x color-spotify"></i>
                                <i v-if="situacaoPlayer === 'play' && conectado" v-on:click="pause" class="fas fa-stop-circle fa-3x color-spotify"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { vueTopprogress } from 'vue-top-progress'

    export default {
        mounted() {
            const self = this;

            setInterval(() => {
                self.getMusicaAtual();
            }, 1000 * 15);

            window.onSpotifyWebPlaybackSDKReady = () => {
                self.player = new Spotify.Player({
                    name: 'Ouvir Juntos',
                    getOAuthToken: cb => {
                        axios.get('/api/getuserdata')
                            .then(res => {
                                const token = res.data.data.spotify_token;
                                cb(token);
                            }).catch(err => {
                            self.$root.$emit('notificar', 'Ocorreu um erro ao buscar o token do usuário. ', 'error');
                            console.log(err);
                        });
                    },
                });

                // Error handling
                self.player.addListener('initialization_error', ({ message }) => { console.error(message); self.situacaoPlayer = 'pause'; });
                self.player.addListener('authentication_error', ({ message }) => { console.error(message); self.situacaoPlayer = 'pause'; });
                self.player.addListener('account_error', ({ message }) => { console.error(message); self.situacaoPlayer = 'pause'; });
                self.player.addListener('playback_error', ({ message }) => {
                    console.error(message);
                    self.situacaoPlayer = 'pause';
                    self.trocarFila(self.musicaAtual.id_fila);
                });

                // Playback status updates
                self.player.addListener('player_state_changed', state => { self.playerStateChanged(state) });

                // Ready
                self.player.addListener('ready', ({ device_id }) => {
                    console.log('Ready with Device ID', device_id);
                    self.situacaoPlayer = 'pause';
                });

                // Not Ready
                self.player.addListener('not_ready', ({ device_id }) => {
                    console.log('Device ID has gone offline', device_id);
                    self.situacaoPlayer = 'pause';
                });

                self.connect();
            };

            Echo.channel('filas').listen('MusicaIniciada', (e) => {
                self.getMusicaAtual();
                // if (e.item.id_fila === self.musicaAtual.id_fila) {
                // }
            });
        },
        components: {
            vueTopprogress
        },
        data() {
            return {
                spotify_token: 'BQC5Oo7Nhn3_nZqT97aNic-yv_uQ3DU7LZp-SjybSQeWLdZllsWr5ebOhelJnVdbo24s88A9Wid-GHAtdJPquUk0RzUhphdWXrDJxzv5onN_yOF8r_vrrKm_JECeS750iVNIPGE4mykVT1Y92qKeNBcEufyZXDTYoLPS04ALKYvlDoX5K87gW-YSrw',
                player: {},
                situacaoPlayer: 'pause',
                musicaAtual: {},
                conectado: false
            }
        },
        methods: {
            connect: function () {
                const self = this;
                // Connect to the player!
                self.player.connect().then(success => {
                    if (!success) {
                        self.$root.$emit('notificar', 'Não foi possível iniciar a música.', 'error');
                        self.conectado = false;
                    } else {
                        self.conectado = true;
                    }
                });
            },
            pause: function() {
                const self = this;
                self.player.pause().then(() => {
                    console.log('Paused!');
                    self.situacaoPlayer = 'pause';
                }).catch(() => {
                    self.situacaoPlayer = 'pause';
                });
            },
            play: function() {
                const self = this;
                self.player.resume().then(() => {
                    console.log('Resumed!');
                    self.situacaoPlayer = 'play';
                }).catch(() => {
                    self.situacaoPlayer = 'pause';
                });
            },
            stop: function() {
                const self = this;
                // Connect to the player!
                self.player.disconnect();
                self.situacaoPlayer = 'pause';
            },
            playerStateChanged: function (state) {
                const self = this;
                if (state) {
                    if (state.paused) {
                        self.situacaoPlayer = 'pause';
                    } else {
                        if (!self.musicaAtual.status) {
                            self.getMusicaAtual();
                        }
                        self.situacaoPlayer = 'play';
                    }
                } else {
                    self.situacaoPlayer = 'pause';
                    self.conectado = false;
                    self.connect();
                }
            },
            getMusicaAtual() {
                const self = this;

                axios.get('/api/getmusicaatual/')
                    .then(res => {
                        self.musicaAtual = res.data;
                    }).catch(err => {
                    console.log(err);
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                })
            },
            getUserData() {
                const self = this;
                axios.get('/api/getuserdata')
                    .then(res => {
                        self.status = res.data.data.spotify_token;
                    }).catch(err => {
                    self.$root.$emit('notificar', 'Ocorreu um erro ao buscar os dados. ', 'error');
                    console.log(err);
                });
            },
            likeMusica() {
                const self = this;
                this.$refs.topProgress.start();
                self.musicaAtual.liked = !self.musicaAtual.liked;

                axios.post('/api/like/'+self.musicaAtual.id)
                    .then(res => {
                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.getMusicaAtual();
                        }
                        this.$refs.topProgress.done();

                    }).catch(err => {
                    console.log(err);
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao dar like na música. ', 'error');
                })

            },
            trocarFila(id) {
                const self = this;
                this.$refs.topProgress.start();
                axios.post('/api/trocarfila', {fila: id,})
                    .then(res => {
                        this.$refs.topProgress.done();

                        if (!res.data.status) {
                            self.$refs.topProgress.fail();
                            self.$root.$emit('notificar', res.data.message, 'error');
                        } else {
                            self.getMusicaAtual();
                        }
                    }).catch(err => {
                    console.log(err);
                    self.$refs.topProgress.fail();
                    this.$refs.topProgress.done();
                    self.$root.$emit('notificar', 'Ocorreu um erro ao iniciar a música. ', 'error');
                })
            }
        }
    }
</script>

<style scoped>
    @import url(https://fonts.googleapis.com/css?family=Raleway:400,200,300,800);

    body {
        font-family: 'Raleway', Arial, sans-serif;
        background-color: #212121;
    }

    body {
        padding: 2rem 0rem;
    }

    .card-player {
        background-color: #1b1e21;
    }

    .card-title {
        margin-bottom: 0.3rem;
    }

    .cat {
        display: inline-block;
        margin-bottom: 1rem;
    }

    .fa-users {
        margin-left: 1rem;
    }

    .color-spotify {
        color: #1db954;
    }

    .item-image:hover {
        -moz-transform: scale(1.1);
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
        box-shadow: 11px 11px 11px rgba(33,33,33,.2);
    }
</style>