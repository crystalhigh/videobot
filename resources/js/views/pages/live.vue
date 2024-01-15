<template>
  <div id="page-live">
    <div class="preview-badge" v-if="preview">
      You are in preview mode, answer won't be submitted!
    </div>
    <div class="preview-badge" v-if="isTemplate">
      You are in template mode, answer won't be submitted!
    </div>
    <div class="limit-reached" v-if="limit">
      <div class="limit-reached-inner">
        <h5 class="text-center">
          Error: If You Are The Owner, Please Login To Your Account:
          <a href="https://vid-gen.com/login">https://vid-gen.com</a> To
          Upgrade.
        </h5>
      </div>
    </div>
    <div v-else>
      <div v-if="(isWidgetShow && pv_id) || !pv_id">
        <div class="live-logo">
          <a href="/">
            <img src="/images/logo-new.png" alt="vidGEN" width="169" height="38"/>
          </a>
        </div>
        <div v-if="currentStep && currentStep !== 'end'" class="live-wrapper">
          <div class="video-wrapper" :class="mobile ? 'mobile' : ''">
            <div
              class="toolbar"
              v-if="started && vidpop && vidpop.advanced.show_control"
            >
              <span class="time">{{ current }} / {{ duration }}</span>
              <span
                class="tool-button"
                :class="showCC ? 'active' : ''"
                @click="showCC = !showCC"
                >CC</span
              >
            </div>
            <div class="timeline" v-if="vidpop && vidpop.advanced.show_control">
              <progress-bar
                type="line"
                ref="line"
                :options="timelineOption"
              ></progress-bar>
            </div>
            <span
              class="overlay-play"
              v-if="!playing && currentStep && currentStep.video_url"
              @click="handlePlay($event, 1)"
              id="start-play"
            >
              <fa-icon :icon="['fas', 'play']" />
            </span>
            <div
              class="video-inner-wrapper"
              :class="currentStep.fit_video ? 'fit-video' : ''"
            >
              <video
                :key="currentStep.video_url"
                ref="videoRef"
                @canplay="playVideo"
                id="live-video"
                @click="handlePlay($event, 0)"
                @timeupdate="handleTime"
                playsinline
              >
                <source
                  :src="getUrl(currentStep.video_url)"
                  :type="getType(currentStep.video_url)"
                />
                <track
                  default
                  kind="captions"
                  srclang="en"
                  :src="getUrl(currentStep.cc_path)"
                  v-if="currentStep.cc_path && showCC"
                />
              </video>
            </div>
            <div class="overlay-wrapper">
              <div class="position-relative w-100 h-100">
                <div class="d-flex align-items-center advertiser-info">
                  <img :src="advertiser.avatar" class="advertiser-avatar" v-if="advertiser.avatar"/>
                  <div class="advertiser-name d-flex align-items-center justify-content-center" v-else>{{ nameAbb }}</div>
                  <span style="font-size: 12px;">{{ advertiser.name }}</span>
                </div>
                <div
                  class="overlay-inner-wrapper"
                  :class="`${currentStep.overlay.position || 'TC'}`"
                >
                  <div class="overlay-text-wrapper">
                    <span
                      :style="{
                        color: currentStep.overlay.color,
                        fontSize: currentStep.overlay.size,
                        backgroundColor: currentStep.overlay.bg_color || 'unset'
                      }"
                      class="text-overlay"
                      v-if="currentStep.overlay.text"
                      v-html="linebreak(currentStep.overlay.text)"
                    >
                    </span>
                    <span
                      v-if="
                        !currentStep.overlay.text && vidpop.advanced.show_title
                      "
                      :style="{
                        color: 'white',
                        backgroundColor: 'black',
                        padding: '5px 10px',
                        fontSize: '1.5rem'
                      }"
                      class="text-overlay"
                    >
                      {{ vidpop.name }}
                    </span>
                  </div>
                </div>
                <div class="brand-wrapper d-flex align-items-center justify-content-between" :style="{ backgroundColor: bgColor }">
                  <a class="brand-wrapper-sponsored-a" @click="onSponsored"><span class="brand-wrapper-sponsored text-white d-flex align-items-center">Sponsored&nbsp;<fa-icon :icon="['fas', 'exclamation-circle']" /></span></a>
                  <span class="brand-wrapper-sponsored text-white d-flex align-items-center">Powered By:&nbsp;
                    <a :href="brand.url" target="_blank" :style="{ color: color }">
                      <img
                        src="/images/logo-new1.png"
                        v-if="brand.name === 'vid-gen.com'"
                      />{{ brand.name === 'vid-gen.com' ? '' : brand.name }}
                    </a>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div
            class="answer-section"
            :class="mobile ? 'mobile' : ''"
            v-if="delayOver"
            >
            <!-- :publisher="publisher" -->
            <!-- <respond-desktop
              v-if="vidpop && !mobile && isVideoLoaded"
              :vidpop="vidpop"
              :pv_id="pv_id"
              :step="currentStep"
              :preview="preview || isTemplate"
              :collected="collected"
              :layoutMode="false"
              :color="color"
              :bgColor="bgColor"
              :advertiser="advertiser"
              @onNext="handleNext"
              @onCollect="handleCollect"
              @onEndText="handleEndText"
              @onEndVideo="handleEndVideo"
              @onEndAudio="handleEndAudio"
              /> -->
              <!-- :publisher="publisher" -->
            <respond-mobile
              v-if="vidpop && isVideoLoaded"
              :vidpop="vidpop"
              :pv_id="pv_id"
              :step="currentStep"
              :preview="preview || isTemplate"
              :collected="collected"
              :layoutMode="false"
              :color="color"
              :bgColor="bgColor"
              :advertiser="advertiser"
              @onNext="handleNext"
              @onCollect="handleCollect"
              @onEndText="handleEndText"
              @onEndVideo="handleEndVideo"
              @onEndAudio="handleEndAudio"
            />
          </div>
        </div>
        <end-screen
          :vidpop="vidpop"
          :brand="brand"
          :bgColor="bgColor"
          :color="color"
          v-else-if="currentStep === 'end'"
        />
        <div v-else class="loader">
          <fa-icon :icon="['fas', 'spinner']" spin class="loading" />
        </div>
      </div>
    </div>
    <b-modal
      id="sponsored-modal"
      hide-footer
      title="Sponsored"
      centered
      ref="sponsoredModal"
      size="md">
      <h6>You are seeing this ad because you visited a vetted publisher of the vid-GEN ad network.</h6>
      <h6>vid-GEN does not have your data.</h6>
      <h6>Please contact the publisher to learn more about your privacy. </h6><br/>
    </b-modal>
  </div>
</template>

<style lang="scss" scoped>
#page-live {
  width: 100%;
  min-height: 100vh;
  background-color: #e4eafa;
  position: relative;
  overflow: hidden;
  .live-logo {
    position: absolute;
    left: 20px;
    top: 20px;
  }
  .advertiser-info {
    position: absolute;
    top: 10px;
    left: 10px;
    color: white;
    width: 100%;
  }
  .advertiser-avatar,.advertiser-name {
    width: 24px;
    height: 24px;
    border-radius: 12px;
    border: 1px solid gray;
    margin-right: 8px;
    font-size: 10px;
  }
  .brand-wrapper-sponsored-a {
    &:hover {
      cursor: pointer;
    }
  }
  .brand-wrapper-sponsored {
    font-weight: 800;
    font-size: 12px;
  }
  .limit-reached {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
  }
  .webvtt {
    font-size: 0.8rem;
    color: red;
  }

  .play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #000000cc;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
    .fa-play {
      font-size: 5rem;
      color: white;
      font-weight: 300;
      &:hover {
        cursor: pointer;
        opacity: 0.75;
      }
    }
  }
  .preview-badge {
    background-color: #1998e4;
    padding: 5px;
    color: white;
    opacity: 0.7;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 10;
  }
  .live-wrapper {
    // width: 100%;
    margin: 0 auto;
    max-width: 400px;
    min-height: 100vh;
    display: flex;
    position: relative;
    .video-wrapper {
      // margin-top: 10vh;
      width: 55%;
      height: 100vh;
      display: flex;
      align-items: center;
      background-color: black;
      position: relative;
      z-index: 1;
      .overlay-play {
        font-size: 2rem;
        color: black;
        width: 80px;
        height: 80px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        // border-radius: 50%;
        border-radius: 8px;
        box-shadow: 0px 0px 16px 6px rgba(0, 0, 0, 0.25);
        background-color: rgba(255, 255, 255, 0.5);
        z-index: 999;
        &:hover {
          cursor: pointer;
          width: 82px;
          height: 82px;
          font-size: 2.1rem;
        }
      }
      .toolbar {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        padding: 5px;
        .time {
          color: white;
          font-weight: 600;
          margin-right: 10px;
        }
        .tool-button {
          font-weight: 600;
          color: white;
          border: 1px solid white;
          border-radius: 5px;
          width: 34px;
          height: 26px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-right: 10px;
          cursor: pointer;
          transition: all 0.3s;
          &.active,
          &:hover {
            background: white;
            color: black;
          }
        }
      }
      .timeline {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        z-index: 1;
      }
      &.mobile {
        width: 100%;
      }
      .video-inner-wrapper {
        width: 100%;
        height: 100%;
        max-height: 100%;
        position: relative;
        pointer-events: auto;
        video {
          width: 100%;
          height: 100%;
          object-fit: cover;
          z-index: 1;
          ::cue {
            font-size: 90%;
          }
          ::-webkit-media-text-track-display {
            font-size: 90%;
          }
        }
        video::cue {
          font-size: 90%;
        }
        video::-webkit-media-text-track-display {
          font-size: 90%;
        }

        &.fit-video video {
          object-fit: contain !important;
        }
      }
      .overlay-wrapper {
        position: absolute;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 1;
        pointer-events: none;
        .overlay-inner-wrapper {
          width: 100%;
          position: absolute;
          .overlay-text-wrapper {
            position: relative;
            .text-overlay {
              padding: 5px 20px;
            }
          }
          &.TL,
          &.TC,
          &.TR {
            top: 10%;
          }
          &.CL,
          &.CC,
          &.CR {
            top: 50%;
            transform: translateY(-50%);
          }
          &.BL,
          &.BC,
          &.BR {
            bottom: 10%;
          }
          &.TL .overlay-text-wrapper .text-overlay,
          &.CL .overlay-text-wrapper .text-overlay,
          &.BL .overlay-text-wrapper .text-overlay {
            margin-left: 5%;
          }
          &.TC .overlay-text-wrapper,
          &.CC .overlay-text-wrapper,
          &.BC .overlay-text-wrapper {
            text-align: center;
          }
          &.TR .overlay-text-wrapper,
          &.CR .overlay-text-wrapper,
          &.BR .overlay-text-wrapper {
            text-align: right;
            .text-overlay {
              margin-right: 5%;
            }
          }
        }
        .brand-wrapper {
          position: absolute;
          bottom: 0px;
          width: 100%;
          padding: 10px;
          opacity: 0.6;
          text-align: center;
          pointer-events: auto;
          img {
            height: 20px;
          }
        }
      }
    }
    .answer-section {
      width: 45%;
      &.mobile {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: calc(100% - 45px);
        z-index: 2;
        pointer-events: none;
      }
    }
  }
  .loader {
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    .loading {
      font-size: 3rem;
      color: #1998e4;
    }
  }
}
</style>

<script>
import axios from 'axios';
import RespondDesktop from '@/views/components/respond-desktop';
import RespondMobile from '@/views/components/respond-mobile';
import EndScreen from '@/views/components/endscreen';

export default {
  name: 'live',
  components: {
    RespondDesktop,
    RespondMobile,
    EndScreen
  },
  data() {
    return {
      code: '',
      vidpop: null,
      steps: [],
      currentStep: null,
      preview: false,
      isTemplate: false,
      mobile: false,
      firstName: '',
      lastName: '',
      email: '',
      street: '',
      // zipcode: '',
      phone: '',
      collected: false,
      data: {},
      group: '',
      color: '#ffffff',
      bgColor: '#1998e4',
      brand: {
        name: 'Vidpopup',
        url: 'https://vid-gen.com'
      },
      delayOver: false,
      started: false,
      playing: false,
      duration: '00:00',
      current: '00:00',
      showCC: true,
      timelineOption: {
        color: '#007aff',
        strokeWidth: 0.5
      },
      awsUrl: 'https://vidpopup.s3.amazonaws.com/',
      limit: false,
      pv_id: 0,
      metricsId: 0,
      isVideoLoaded: false,
      publisher: null,
      advertiser: null,
      isWidgetShow: false,
      nameAbb: '',
    };
  },
  async mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
    this.code = this.$route.params.code;
    this.pv_id = Number.parseInt(this.$route.query.vidgenID);
    if (this.pv_id) {
      await axios.get('/api/published-status?pv_id='+this.pv_id)
      .then(res => {
        this.isWidgetShow = res.data.status == "Approved" && res.data.visible == 1
      })
      .catch(err => {
        console.log('err =>', err);
      })
    }
    this.preview = this.$route.query.preview === '1' ? true : false;
    // this.mobile = this.$route.query.mobile === '1' ? true : false;
    // if (window.innerWidth < 520) {
    //   this.mobile = true;
    // }
    this.mobile = true;
    if (this.code === 'error') {
      this.$router.push({ name: 'error' });
    }
    let tmp_add_url = '';
    if (this.pv_id)
      tmp_add_url = '&pv_id=' + this.pv_id;
    axios
      .get(`/api/live?code=${this.code}${tmp_add_url}`)
      .then(res => {
        if (res && res.status === 200) {
          this.publisher = res.data.publisher;
          this.advertiser = res.data.advertiser;
          const name = this.advertiser.name.split(' ');
          if (name.length > 2)
            this.nameAbb = `${name[0].charAt(0).toUpperCase()}${name[1].charAt(0).toUpperCase()}`;
          else {
            const namelst = name.map(n => n.charAt(0).toUpperCase());
            this.nameAbb = namelst.join('');
          }
          this.vidpop = res.data.vidpop;
          this.steps = res.data.steps;
          this.color = res.data.color;
          this.bgColor = res.data.bg_color;
          this.isTemplate = res.data.vidpop.is_template === '1' ? true : false;
          this.brand = {
            name: res.data.brand,
            url: res.data.url
          };
          this.currentStep = this.steps[0];
          const tmp = [];
          res.data.steps.forEach(st => {
            tmp.push({
              id: st.id,
              type: '',
              value: ''
            });
          });

          this.data = {
            firstName: '',
            lastName: '',
            email: '',
            street: '',
            // zipcode: '',
            phone: '',
            steps: tmp
          };

          this.timelineOption.color = res.data.bg_color;
        } else {
          // redirect to not found
          if (res.data.error === 'limit') {
            this.limit = true;
          } else {
            // this.$router.push({ name: 'error' });
          }
        }
      })
      .catch(err => {
        this.$router.push({ name: 'error' });
      })
      .finally(() => {});
  },
  methods: {
    onSponsored() {
      this.$bvModal.show('sponsored-modal');
    },
    handleNext(idx) {
      this.isVideoLoaded = false;
      
      const ids = this.currentStep.next_step.split(',');
      const id = ids[idx];
      this.playing = false;
      this.started = false;
      this.delayOver = false;

      if (this.currentStep.answer.type === 1) {
        // multiple choice
        this.data = {
          type: 'multiple',
          value: idx
        };
      } else if (this.currentStep.answer.type === 2) {
        this.data = {
          type: 'button',
          value: 'Clicked'
        };
      } else if (this.currentStep.answer.type === 3) {
        this.data = {
          type: 'calendly',
          value: 'Book'
        };
      } else if (this.currentStep.answer.type === 4) {
        this.data = {
          type: 'payment',
          value: 'Paid'
        };
      } else {
        this.data = {
          type: 'text',
          value: 'Logo'
        };
      }
      let postData = this.data;
      if (this.collected && this.email && this.firstName && this.lastName && this.street && this.phone) {
        postData = {
          ...this.data,
          email: this.email,
          firstName: this.firstName,
          lastName: this.lastName,
          street: this.street,
          // zipcode: this.zipcode,
          phone: this.phone
        };
      }
      if ((this.preview || this.isTemplate) && id === 'end') {
        this.currentStep = 'end';
      } else if (this.preview) {
        const nextStep = this.steps.find(s => s.id === id);
        if (nextStep) {
          this.currentStep = nextStep;
        }
      }
      if (this.preview || this.isTemplate) {
        if (id === 'end') {
          this.currentStep = 'end';
        } else {
          const nextStep = this.steps.find(s => s.id === id);
          if (nextStep) {
            this.currentStep = nextStep;
          }
        }
        if (this.collected && this.email && this.name) {
          this.email = '';
          this.name = '';
        }
        this.data = {};
      } else {
        // this.updateMetrics();
        const loader = this.$loading.show();
        axios
          .post('/api/save-reply', {
            vid: this.vidpop.id,
            sid: this.currentStep.id,
            pv_id: this.pv_id,
            group: this.group,
            ...postData
          })
          .then(res => {
            if (res && res.status === 200) {
              this.group = res.data.group;
              if (id === 'end') {
                this.currentStep = 'end';
              } else {
                const nextStep = this.steps.find(s => s.id === id);
                if (nextStep) {
                  this.currentStep = nextStep;
                }
              }
              if (this.collected && this.email && this.name) {
                this.email = '';
                this.name = '';
              }
              this.data = {};
            } else {
              this.$func.showTextMessage(
                'Error',
                'Cannot reply to this VidGen!',
                'error'
              );
            }
          })
          .catch(err => {
            console.log('err =>', err);
            this.$func.showTextMessage(
              'Error',
              'Cannot reply to this VidGen!',
              'error'
            );
          })
          .finally(() => {
            loader && loader.hide();
          });
      }
    },
    handleCollect(info) {
      this.collected = true;
      this.firstName = info.first_name;
      this.lastName = info.last_name;
      this.email = info.email;
      this.street = info.street;
      // this.zipcode = info.zipcode;
      this.phone = info.phone;
      this.handleNext(info.idx);
    },
    updateMetrics() {
      if (this.metricsId != 0) {
        axios
          .put(`/api/metrics/${this.metricsId}`, {
          })
          .then(res => {
            if (res && res.status === 200) {

            } else {
              this.$func.showTextMessage(
                'Error',
                'Cannot update VidGen metrics now!',
                'error'
              );
            }
          })
          .catch(err => {
            console.log('err=>', err)
            this.$func.showTextMessage(
              'Error',
              'Cannot update VidGen metrics now!',
              'error'
            );
          });
      }
    },
    handleEndText(text) {
      // this.updateMetrics();
      this.data = {
        type: 'text',
        value: text
      };

    },
    handleEndVideo(info) {
      // this.updateMetrics();
      this.data = {
        type: 'video',
        value: info.path
      };

    },
    handleEndAudio(info) {
      // this.updateMetrics();
      this.data = {
        type: 'audio',
        value: info.path
      };
    },
    async playVideo() {
      if (this.mobile) {
        setTimeout(() => {
          const startButton = document.getElementById('start-play');
          if (startButton) {
            startButton.click();
          }
        }, 200);
      }
      this.isVideoLoaded = this.$refs.videoRef != undefined ? true : false
      this.duration = this.$func.formatTime(this.$refs.videoRef != undefined ? this.$refs.videoRef.duration : 0);
      this.current = this.$func.formatTime(0);
    },
    handlePlay(e, status) {
      e.preventDefault();
      e.stopPropagation();
      if (!this.started && !this.preview && !this.isTemplate) {
        axios
          .post('/api/impression', {
            code: this.code,
            pv_id: this.pv_id,
            index: this.currentStep.index
          })
          .then(res => {
            if (res && res.status === 200)
              this.metricsId = res.data.metrics_id
          })
          .catch((err) => {console.log('err=>', err)});
      }
      if (status === 1) {
        this.playing = true;
        this.$refs.videoRef.muted = false;
        if (!this.started) {
          // seek to 0
          this.started = true;
          if (this.currentStep.video_delay > 0) {
            setTimeout(() => {
              this.delayOver = true;
            }, this.currentStep.video_delay * 1000);
          } else {
            this.delayOver = true;
          }
        }
        this.$refs.videoRef.play();
      } else {
        this.playing = false;
        this.$refs.videoRef.pause();
      }
    },
    handleTime(e) {
      if (this.vidpop && this.vidpop.advanced.show_control) {
        this.current = this.$func.formatTime(this.$refs.videoRef.currentTime);
        const d = this.$refs.videoRef != undefined ? this.$refs.videoRef.duration : 0;
        const t = this.$refs.videoRef.currentTime;

        this.$refs.line.set(t / d);
      }
    },
    linebreak(content) {
      if (content) {
        return content.replace(/\n/g, '<br />');
      }
      return '';
    },
    getType(url) {
      if (url.endsWith('webm')) {
        return 'video/webm';
      }
      return 'video/mp4';
    },
    getUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    },
  }
};
</script>