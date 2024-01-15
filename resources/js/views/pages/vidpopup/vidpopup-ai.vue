<template>
  <div id="page-vidpopup-ai">
    <div class="ai-video-wrapper">
      <h3 class="text-center mb-4">Your AI Videos</h3>
    </div>
    <b-container class="d-flex justify-content-center">
      <div class="vidpopup-ai">
        <b-row class="vidpopup-ai-list">
          <b-col md="12" v-for="(itm, idx) in videos" :key="`ai-video-${idx}`">
            <div
              class="ai-video-row"
              @click="handleSelect(itm)"
              :class="video === itm ? 'active' : ''"
            >
              <div class="d-flex align-items-center">
                <div class="ai-thumb">
                  <img
                    :src="itm.thumbnail ? `${awsUrl}${itm.thumbnail}` : ''"
                    v-if="itm.status === 1"
                  />
                  <fa-icon
                    :icon="['far', 'play-circle']"
                    class="ai-video-play"
                    @click="playVideo(itm)"
                    v-if="itm.status === 1"
                  />
                </div>
              </div>
              <div class="ai-status-wrapper">
                <div>
                  <h5 class="mb-0">
                    {{ itm.title }}
                  </h5>
                  <span
                    class="ai-status"
                    :class="itm.status === 1 ? 'ready' : ''"
                    >{{ itm.status === 1 ? 'Ready' : 'Processing' }}</span
                  >
                </div>
                <div>
                  <span class="ai-created">{{
                    formatDate(itm.created_at)
                  }}</span>
                  <fa-icon
                    :icon="['fas', 'trash']"
                    class="ai-remove"
                    @click="handleDelete($event, itm)"
                  />
                </div>
              </div>
            </div>
          </b-col>
        </b-row>
        <b-row class="mt-3">
          <div class="d-flex justify-content-between w-100">
            <button type="button" class="btn btn-info ml-4" @click="handleBack">
              Back
            </button>
            <div>
              <button
                type="button"
                class="btn btn-primary mr-2"
                @click="handleReload"
              >
                Reload
              </button>
              <button
                type="button"
                class="btn btn-primary mr-2"
                @click="handleNew"
              >
                Create New
              </button>
              <button
                type="button"
                class="btn btn-success mr-4"
                @click="handleContinue"
              >
                Continue
              </button>
            </div>
          </div>
        </b-row>
      </div>
    </b-container>
    <FsLightbox :toggler="togglerVideo" :sources="videoSource" type="video" />
  </div>
</template>

<style lang="scss" scoped>
#page-vidpopup-ai {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  .vidpopup-ai {
    width: 700px;
    max-width: 100%;
    padding: 30px;
    background-color: white;
    border-radius: 15px;
    .vidpopup-ai-list {
      max-height: 500px;
      overflow-y: auto;
      .ai-video-row {
        display: flex;
        align-content: center;
        cursor: pointer;
        padding: 10px;
        &:hover {
          background-color: #f8f8f8;
        }
        &.active {
          background-color: #f1f1f1;
        }
        .ai-thumb {
          position: relative;
          width: 120px;
          border: 1px solid #ccc;
          background-color: #444;
          min-height: 70px;
          img {
            width: 100%;
            min-height: 70px;
          }
          .ai-video-play {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            font-size: 2rem;
            color: white;
            &:hover {
              opacity: 0.75;
            }
          }
        }
        .ai-status-wrapper {
          display: flex;
          align-items: center;
          justify-content: space-between;
          font-size: 1rem;
          margin-left: 10px;
          width: 100%;
          .ai-status {
            font-weight: 700;
            font-size: 0.8rem;
            color: #d84b4b;
            &.ready {
              color: #2fa360;
            }
          }
          .ai-created {
            margin-left: 20px;
            font-size: 0.8rem;
          }
          .ai-remove {
            color: #d84b4b;
            margin-left: 20px;
            cursor: pointer;
            &:hover {
              opacity: 0.75;
            }
          }
        }
      }
    }
    .btn-continue {
      width: 50%;
      background-color: black;
      color: white;
      font-size: 1.2rem;
      border-radius: 0px;
      padding: 10px 30px;
      &:hover {
        background-color: #333;
        color: #f1f1f1;
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import FsLightbox from 'fslightbox-vue';
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';

export default {
  name: 'vidpopup-ai',
  components: {
    FsLightbox
  },
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      id: '',
      step_id: '',
      index: -1,
      videos: [],
      togglerVideo: false,
      videoSource: [],
      video: null,
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
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
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.index = this.$route.query.index;
    this.loadVideo(true);
  },
  methods: {
    formatDate(dt) {
      return this.$func.formatDate(dt);
    },
    loadVideo(bInit) {
      const loader = this.$loading.show();
      axios
        .get('/api/ai-videos')
        .then(res => {
          if (res && res.status === 200) {
            this.videos = res.data.videos;
            if (bInit && this.videos.length === 0) {
              this.$router.push({
                path: `/app/ai-video`
              });
            }
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(() => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load ai-videos',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    playVideo(itm) {
      this.videoSource = [];
      const url = `${this.awsUrl}${itm.url}`;
      // const url = `/storage/${itm.url}`;
      this.videoSource.push(url);
      this.togglerVideo = !this.togglerVideo;
    },
    handleDelete(e, itm) {
      e.stopPropagation();
      Swal.fire({
        title: 'Warning',
        text: 'Your AI Video will be removed permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          axios
            .delete(`/api/ai-delete/${itm.id}`)
            .then(res => {
              if (res && res.status === 200) {
                this.$func.makeToast(
                  this,
                  'Notice',
                  'Video removed!',
                  'success'
                );
                const idx = this.videos.findIndex(v => v.id === itm.id);
                if (idx > -1) {
                  this.videos.splice(idx, 1);
                }
              } else {
                this.$func.makeToast(this, 'Error', res.data.error, 'danger');
              }
            })
            .catch(() => {
              this.$func.makeToast(
                this,
                'Error',
                'Cannot delete video!',
                'danger'
              );
            })
            .finally(() => {
              loader && loader.hide();
            });
        }
      });
    },
    handleSelect(itm) {
      this.video = itm;
    },
    handleNew() {
      if (!this.currentUser.deep_api || !this.currentUser.deep_secret) {
        this.$func.showHtmlMessage(
          `Notice`,
          `<h5>You don't have credential for it!</h5><h5>Add your key on profile page!</h5>`,
          'info'
        );
      } else {
        this.$router.push({
          path: `/app/ai-video`
        });
      }
    },
    handleContinue() {
      if (!this.video) {
        this.$func.showTextMessage('Notice', 'Please select video', 'info');
        return;
      }
      if (this.video.status !== 1) {
        this.$func.showTextMessage(
          'Notice',
          'Your video is still in progress, try later!',
          'info'
        );
        return;
      }
      const loader = this.$loading.show();
      axios
        .post(`/api/createStep-ai`, {
          vid: this.id,
          sid: this.step_id,
          index: this.index,
          aid: this.video.id
        })
        .then(res => {
          if (res && res.status === 201) {
            loader && loader.hide();
            this.$router.push({
              path: `/app/vidgen/${this.id}/${res.data.id}/edit/overlay`
            });
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot create step right now!',
              'danger'
            );
          }
        })
        .catch(() => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot create step right now!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleReload() {
      this.loadVideo(false);
    },
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/manage?index=${this.index}`
      });
    }
  }
};
</script>
