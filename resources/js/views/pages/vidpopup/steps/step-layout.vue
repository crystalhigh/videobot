<template>
  <div id="page-step-layout">
    <div class="preview-section" v-if="currentStep !== null">
      <div class="d-flex step-index-wrapper">
        <span
          class="step-index-badge"
          v-for="(s, idx) in steps"
          :key="`step-index-${idx}`"
          :class="currentStep.id === s.id ? 'active' : ''"
          @click="gotoStep(s.id)"
        >
          Step{{ s.index }}
        </span>
        <span class="step-index-badge" @click="gotoStep('newStep')">
          + New
        </span>
      </div>
      <fa-icon
        :icon="['fas', 'trash-alt']"
        class="step-index-remove"
        @click="removeStep"
      />
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
                muted
                loop
                :key="currentStep.video_url"
                ref="videoRef"
                id="step-video"
                playsinline
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
                  :class="`${currentStep.overlay.position || 'TC'}`"
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
              :vidpop="vidpop"
              :pv_id=0
              :step="step"
              :preview="true"
              :collected="false"
              v-if="viewMode === 'desktop'"
              :color="color"
              :bgColor="bgColor"
              :layoutMode="true"
            />
            <!-- :publisher="{}" -->
            <respond-mobile
              :pv_id=0
              :vidpop="vidpop"
              :step="step"
              :preview="true"
              :fitVideo="currentStep.fit_video ? true : false"
              v-if="viewMode === 'mobile'"
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
    <div class="step-content" v-if="loaded">
      <router-view></router-view>
    </div>
  </div>
</template>
<style lang="scss" scoped>
$base-color: #1998e4;
#page-step-layout {
  border-radius: 30px;
  height: 100%;
  display: flex;
  .preview-section {
    width: 65%;
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    background-color: #e4eafa;

    .step-index-remove {
      position: absolute;
      top: 20px;
      right: 60px;
      font-size: 1.5rem;
      color: #cf4b4b;
      cursor: pointer;
      &:hover {
        color: #cc8282;
      }
    }

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
          width: 55%;
          height: auto;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #b2dcf5;
          position: relative;
          .video-duration {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            background-color: black;
            padding: 3px 7px;
            z-index: 5;
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
  .step-content {
    width: 35%;
    background-color: white;
    border-top-right-radius: 15px;
    border-bottom-right-radius: 15px;
    padding: 20px;
    padding-top: 20px;
    @media (min-width: 1500px) {
      width: 30%;
      padding: 30px;
    }
  }
  .step-index-wrapper {
    display: flex;
    position: absolute;
    left: 60px;
    top: 18px;
    .step-index-badge {
      font-size: 1rem;
      background-color: #3490dc;
      padding: 2px 10px;
      border-radius: 30px;
      color: white;
      margin-right: 10px;
      cursor: pointer;
      &:hover {
        opacity: 0.75;
      }
      &.active {
        color: black;
        background-color: #fff;
        border: 1px solid #3490dc;
      }
    }
  }
}
</style>

<script>
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';
import { VIDPOP, STEP, UPDATE_DURATION } from '@/services/store/vidpopup.module';
import RespondDesktop from '@/views/components/respond-desktop';
import RespondMobile from '@/views/components/respond-mobile';
export default {
  name: 'step-layout',
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
    }
  },
  data() {
    return {
      vidpop_id: '',
      step_id: '',
      user_id: 0,
      currentStep: null,
      currentVidpop: null,
      viewMode: 'desktop',
      loaded: false,
      steps: [],
      color: '#ffffff',
      bgColor: '#1998e4',
      brand: {
        name: 'Vidpopup',
        url: 'https://vid-gen.com'
      },
      playing: false,
      awsUrl: '',
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
    this.vidpop_id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.loadPage(this.vidpop_id, this.step_id);
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    async loadPage(vid, sid) {
      const loader = this.$loading.show();
      try {
        const [steps, vidpop] = await Promise.all([
          axios.get(`/api/steps?vid=${vid}`),
          axios.get(`/api/vidpop/${vid}`)
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
        if (steps.data) {
          const step = steps.data.find(s => s.id === sid);
          this.$store.dispatch(STEP, step);
          this.steps = steps.data;
        }
        this.loaded = true;
      } catch (err) {
        // console.log(err);
      }
      loader && loader.hide();
    },
    removeStep() {
      Swal.fire({
        title: 'Warning',
        text: 'Current step will be removed permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          try {
            axios
              .delete(`/api/step/${this.step_id}`)
              .then(res => {
                if (res && res.status === 204) {
                  this.$store.dispatch(STEP, {});
                  this.$router.push({ path: '/app/vidgen' });
                  loader && loader.hide();
                }
              })
              .catch(err => {
                loader && loader.hide();
              });
          } catch (err) {
            loader && loader.hide();
          }
        }
      });
    },
    gotoStep(id) {
      if (id === this.currentStep.id) {
        return;
      }
      if (id === 'newStep') {
        this.$router.push({
          path: `/app/vidgen/${this.vidpop_id}/newStep/manage?index=-1`
        });
      } else {
        this.step_id = id;
        this.loadPage(this.vidpop_id, id);
        this.$router.push({
          path: `/app/vidgen/${this.vidpop_id}/${id}/edit/overlay`
        });
      }
    },
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
          this.$store.dispatch(UPDATE_DURATION, this.duration);
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
