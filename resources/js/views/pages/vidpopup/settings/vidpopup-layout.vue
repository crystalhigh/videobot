<template>
  <div id="page-vidpopup-layout">
    <div class="preview-section" v-if="currentStep !== null">
      <div
        class="inner-window"
        :class="currentStep.fit_video ? 'fit-video' : ''"
      >
        <div
          class="preview-window"
          :class="viewMode === 'mobile' ? 'mobile' : ''"
        >
          <div class="video-wrapper">
            <span class="video-duration" v-if="duration > 0">{{ formatedTime }}</span>
            <div class="video-inner-wrapper">
              <video
                autoplay
                loop
                muted
                :key="currentStep.video_url"
                ref="videoRef"
                @canplay="loadDuration"
              >
                <source
                  :src="getUrl(currentStep.video_url)"
                  :type="getType(currentStep.video_url)"
                />
              </video>
            </div>
            <div class="overlay-wrapper">
              <div
                class="position-relative w-100 h-100"
                @click="handlePlay($event, 0)"
              >
                <div
                  class="overlay-inner-wrapper"
                  :class="`${currentStep.overlay.position || 'BL'}`"
                >
                  <div class="overlay-text-wrapper">
                    <span
                      :style="{
                        color: currentStep.overlay.color,
                        fontSize: currentStep.overlay.size,
                        backgroundColor: currentStep.overlay.bg_color || 'unset'
                      }"
                      v-if="currentStep.overlay.text !== ''"
                      class="text-overlay"
                      v-html="linebreak(currentStep.overlay.text)"
                    >
                    </span>
                    <span
                      v-if="
                        currentStep.overlay.text === '' &&
                          vidpop.advanced.show_title
                      "
                      class="text-overlay"
                    >
                      {{ vidpop.name }}
                    </span>
                  </div>
                </div>
                <div
                  class="brand-wrapper"
                  :style="{ backgroundColor: bgColor }"
                >
                  <a
                    :href="brand.url"
                    target="_blank"
                    :style="{ color: color }"
                  >
                    <img
                      src="/images/logo-new1.png"
                      style="width: 40px;"
                      v-if="brand.name === 'vid-gen.com'"
                    />{{ brand.name === 'vid-gen.com' ? '' : brand.name }}
                  </a>
                </div>
                <span
                  class="overlay-play"
                  v-if="!playing && currentStep && currentStep.video_url"
                  @click="handlePlay($event, 1)"
                >
                  <fa-icon :icon="['fas', 'play']" />
                </span>
              </div>
            </div>
          </div>

          <div class="answer-section">
            <!-- :publisher="{}" -->
            <respond-desktop
              v-if="viewMode === 'desktop'"
              :vidpop="vidpop"
              :pv_id=0
              :step="step"
              :preview="true"
              :collected="false"
              :color="color"
              :bgColor="bgColor"
              :layoutMode="true"
            />
            <!-- :publisher="{}" -->
            <respond-mobile
              v-if="viewMode === 'mobile'"
              :pv_id=0
              :vidpop="vidpop"
              :step="step"
              :preview="true"
              :fitVideo="step.fit_video ? true : false"
              :color="color"
              :bgColor="bgColor"
              :layoutMode="true"
            />
          </div>
        </div>
      </div>

      <div class="view-mode-buttons">
        <fa-icon
          :icon="['fas', 'desktop']"
          class="mr-3 view-mode-icon"
          :class="viewMode === 'desktop' ? 'active' : ''"
          @click="viewMode = 'desktop'"
        />
        <fa-icon
          :icon="['fas', 'mobile-alt']"
          class="view-mode-icon"
          :class="viewMode === 'mobile' ? 'active' : ''"
          @click="viewMode = 'mobile'"
        />
      </div>
    </div>
    <div class="vidpopup-content" v-if="loaded">
      <router-view></router-view>
    </div>
  </div>
</template>
<style lang="scss" scoped>
$base-color: #1998e4;
#page-vidpopup-layout {
  border-radius: 30px;
  height: 100%;
  display: flex;
  .preview-section {
    width: 60%;
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    background-color: #e4eafa;

    .inner-window {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 1;
      padding: 50px 25px 25px 25px;
      .preview-window {
        display: flex;
        height: 100%;
        width: 100%;
        overflow: hidden;
        border: 3px solid #b5daf7;
        border-radius: 15px;
        position: relative;
        max-height: 75vh;
        .video-wrapper {
          width: 60%;
          height: auto;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #b2dcf5;
          position: relative;
          z-index: 1;
          .video-duration {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            background-color: black;
            padding: 3px 7px;
          }
          .video-inner-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            align-items: center;
            video {
              width: 100%;
              height: 100%;
              object-fit: cover;
              z-index: 1;
              max-height: 100%;
            }
          }
          .overlay-wrapper {
            position: absolute;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
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
            }
          }
        }
        .answer-section {
          width: 45%;
          height: 100%;
        }
        &.mobile {
          width: 375px;
          .video-wrapper {
            width: 100% !important;
          }
          .answer-section {
            position: absolute;
            left: 0px;
            top: 0px;
            width: 100%;
            z-index: 2;
          }
        }
      }

      &.fit-video video {
        object-fit: contain !important;
      }
    }
    .view-mode-buttons {
      margin-bottom: 15px;
      text-align: center;
      .view-mode-icon {
        color: #ccc;
        font-size: 2rem;
        cursor: pointer;
        &.active {
          color: #333;
        }
        &:hover:not(.active) {
          color: #888;
        }
      }
    }
    @media (min-width: 1500px) {
      width: 70%;
    }
  }
  .vidpopup-content {
    width: 40%;
    background-color: white;
    border-top-right-radius: 15px;
    border-bottom-right-radius: 15px;
    padding: 20px;
    padding-top: 40px;
    @media (min-width: 1500px) {
      width: 30%;
      padding: 40px 30px 30px 30px;
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import { VIDPOP, STEP } from '@/services/store/vidpopup.module';
import RespondDesktop from '@/views/components/respond-desktop';
import RespondMobile from '@/views/components/respond-mobile.vue';
export default {
  name: 'vidpopup-layout',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser'])
  },
  components: {
    RespondDesktop,
    RespondMobile
  },
  watch: {
    step: function(newVal, oldVal) {
      this.currentStep = newVal;
    },
    vidpop: function(newVal, oldVal) {
      this.currentVidpop = newVal;
      if (newVal.brand != this.currentVidpop) {
        const tmp = this.brands.find(b => b.id === newVal.brand);
        if (tmp) {
          this.brand = {
            name: tmp.name,
            id: tmp.id,
            url: tmp.url
          };
          this.color = tmp.color || '#ffffff';
          this.bgColor = tmp.bg_color || '#1998e4';
        } else {
          this.brand = {
            name: 'vid-gen.com',
            url: 'https://vid-gen.com'
          };
          this.color = '#ffffff';
          this.bgColor = '#1998e4';
        }
      }
    }
  },
  data() {
    return {
      vidpop_id: '',
      step_id: 0,
      user_id: 0,
      currentStep: null,
      currentVidpop: null,
      viewMode: 'desktop',
      loaded: false,
      color: '#ffffff',
      bgColor: '#1998e4',
      brand: {
        name: 'vid-gen.com',
        url: 'https://vid-gen.com'
      },
      brands: [
        {
          name: 'vid-gen.com',
          url: 'https://vid-gen.com',
          id: ''
        }
      ],
      playing: false,
      awsUrl: 'https://vidpopup.s3.amazonaws.com/',
      duration: 0,
      formatedTime: '00:00'
    };
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'agent')) {
      this.$func.makeToast(
        this,
        'Error',
        'You are not allowed to visit this page!',
        'danger'
      );
      if (this.currentUser.role == 'admin')
        this.$router.push({path: '/app/admin-payout'});
      else
        this.$router.push({path: '/app/statistics'});
    }
  },
  async mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
    this.vidpop_id = this.$route.params.id;
    const loader = this.$loading.show();
    try {
      const [step, vidpop, brands] = await Promise.all([
        axios.get(`/api/first-step?v_id=${this.vidpop_id}`),
        axios.get(`/api/vidpop/${this.vidpop_id}`),
        axios.get(`/api/brands/`)
      ]);
      if (vidpop.data) {
        this.$store.dispatch(VIDPOP, vidpop.data.vidpop);
        this.color = vidpop.data.color;
        this.bgColor = vidpop.data.bg_color;
        this.brand = {
          name: vidpop.data.brand,
          url: vidpop.data.url
        };
      }
      if (step.data) {
        this.$store.dispatch(STEP, step.data);
        this.step_id = step.data.id;
      }
      if (brands && brands.status === 200) {
        brands.data.forEach(element => {
          this.brands.push(element);
        });
      }
      this.loaded = true;
    } catch (err) {
      // console.log(err);
    }
    loader && loader.hide();
  },
  methods: {
    handlePlay(e, status) {
      e.preventDefault();
      e.stopPropagation();
      if (status === 1) {
        this.playing = true;
        this.$refs.videoRef.muted = false;
        this.$refs.videoRef.play();
      } else {
        this.playing = false;
        this.$refs.videoRef.pause();
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
    loadDuration() {
      if (this.$refs.videoRef && this.$refs.videoRef.duration !== Infinity) {
        window.URL.revokeObjectURL(this.getUrl(this.currentStep.video_url));
        if (typeof this.$refs.videoRef.duration) {
          this.duration = this.$refs.videoRef.duration;
          this.formatedTime = this.$func.formatTime(this.duration);
        }
      }
    },
    getUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
