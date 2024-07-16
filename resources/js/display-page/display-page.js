const { forEach } = require("lodash");

if (document.getElementById("display-page")) {
    var app = {
        data() {
            return {
                time_out: null,
                tokens: [],
                today: window?.JLToken.date_for_display ?? null,
                queue: [],
                isProcessing: false,
                isPlaying: false,
                from_first: true,
                previous_data: [],
                response_data: [],
                audio: window?.JLToken.audioEl,
                token_for_sound: null
            }
        },
        methods: {
            getTokens() {
                axios.get(`${window?.JLToken.get_tokens_for_display_url}?time=${new Date().getTime()}`).then(res => {

                    if (!this.from_first && res.data && res.data.length && res.data[0].called_date == this.today) {
                        this.response_data = res.data.map(res => {
                            delete res.counter_time;
                            return res;
                        });
                        if (JSON.stringify(this.previous_data) == JSON.stringify(this.response_data)) {
                            this.previous_data = this.response_data;
                            this.time_out = setTimeout(() => {
                                this.processQueue();
                                this.getTokens();
                            }, 1000)
                        } else {
                            this.previous_data = this.response_data;
                            this.queue.push(res.data);
                            this.processQueue();
                            this.time_out = setTimeout(() => {
                                this.getTokens();
                            }, 1000)
                        }
                    }
                    else if (this.from_first && res.data && res.data.length && res.data[0].called_date == this.today) {

                        this.tokens = res.data;
                        this.disableLoader();
                        this.previous_data = res.data.map(res => {
                            delete res.counter_time;
                            return res;
                        });
                        this.from_first = false;
                        this.time_out = setTimeout(() => {
                            this.getTokens();
                        }, 1000)
                    }
                    else {

                        this.disableLoader();
                        this.from_first = false;
                        this.time_out = setTimeout(() => {
                            this.getTokens();
                        }, 1000)
                    }

                })
                    .catch(err => {
                        this.disableLoader();
                        this.time_out = setTimeout(() => {
                            this.getTokens();
                        }, 1000)
                    })
            },

            processQueue() {

                if (!this.isProcessing && !this.isPlaying && this.queue.length) {
                    this.isProcessing = true;
                    if (this.queue.length && (this.queue[0][0]?.id != this.tokens[0]?.id && this.tokens[0]?.token_letter == this.queue[0][0]?.token_letter && this.tokens[0]?.token_number == this.queue[0][0]?.token_number)) {
                        this.tokens = this.queue[0];

                        this.queue.shift();
                        if (this.tokens[0].call_status_id == null) {
                            if(window?.JLToken?.voice_enabled == 1)this.playAudio(this.tokens[0]);
                            this.popup(this.tokens[0])
                        }
                    }
                    else if (this.queue.length && ((this.queue[0][0].id != this.tokens[0]?.id) || (this.tokens == null && this.queue[0][0].call_status_id == null))) {

                        this.tokens = this.queue[0];
                        this.queue.shift();

                        if (this.tokens[0].call_status_id == null) {
                            if(window?.JLToken?.voice_enabled == 1) this.playAudio(this.tokens[0]);
                            this.popup(this.tokens[0])
                        }
                    }
                    else {

                        this.tokens = this.queue[0];
                        this.queue.shift();
                    }
                    this.isProcessing = false;
                }

            },

            disableLoader() {
                $('body').addClass('loaded');
            },

            popup(token) {
                Swal.fire({
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: 'swal-wide',
                    html: '<h1 style="color:#000; font-size:70px">' + token.token_letter + '-' + token.token_number + '</h1><h3 style="font-size:40px">' + token.counter.name + '</h3>',
                });
            },
            playAudio(token) {
                let promise = this.audio.play();


                if (promise !== undefined) {
                    promise.then(_ => {
                        this.isPlaying = true;
                        this.token_for_sound = token
                    }).catch(error => {
                        this.processQueue();
                    });
                }

            }
        },
        mounted() {
            if(window?.JLToken?.voice_enabled == 1){
                this.audio.addEventListener("ended", () => {
                    if (this.token_for_sound) {
                        let voice = `${window?.JLToken?.voice_content_one} ${this.token_for_sound.token_letter.toString().split('').join(' ')} ${this.token_for_sound.token_number.toString().split('').join(' ')} ${window?.JLToken?.voice_content_two} ${this.token_for_sound.counter.name}`;
                        responsiveVoice.speak(voice, window?.JLToken?.voice_type, {
                            rate: 0.75,
                            onend: () => {
                                this.token_for_sound = null;
                                this.isPlaying = false;
                                this.processQueue()
                            }
                        });
                    }
                });
            }
            this.getTokens();
        },

        unmounted() {
            clearInterval(this.time_out);
        }
    };
    Vue.createApp(app).mount('#display-page')

}