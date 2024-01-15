<template>
  <div class="ai-layout">
    <div class="buy-key-guide" v-if="noKey">
      <h3 class="text-center">You didn't add your deepword key!</h3>
      <h5 class="text-center mt-3">
        <a href="https://www.deepword.co/" target="blank"
          >Get your key from here!</a
        >
      </h5>
      <h5 class="text-center mt-3">
        <a href="/app/settings">If you already have your key, add it here!</a>
      </h5>
    </div>
    <div class="preview-section" v-if="!noKey">
      <div class="preview-wrapper">
        <div class="video-wrapper">
          <div
            v-if="bgType === 'solid'"
            class="video-background"
            :style="{ backgroundColor: bg }"
          ></div>
          <div v-else class="video-background">
            <img :src="bg" />
          </div>
          <video autoplay loop muted :key="video">
            <source :src="video" type="video/mp4" />
          </video>
        </div>
        <div class="audio-wrapper">
          <audio controls ref="audioRef">
            <source :src="audio ? `/storage/${audio}` : ''" type="audio/mpeg" />
          </audio>
        </div>
      </div>

      <div class="video-script-wrapper">
        <ai-script @updateSound="updateSound" />
        <span class="user-credit"
          >Available credits : {{ currentUser.credit.toFixed(2) }}</span
        >
      </div>
    </div>
    <div class="setting-section" v-if="!noKey">
      <ai-avatar
        v-if="pane === 'avatar'"
        :avatar="avatar"
        :avatars="avatars"
        :title="title"
        :size="avatarSize"
        @onNext="onNext"
        @onSelectAvatar="onSelectAvatar"
      />
      <ai-background
        v-else-if="pane === 'background'"
        @onPrev="pane = 'avatar'"
        @onBackground="handleBackground"
        @onRender="handleRender"
      />
    </div>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
$preview-color: #e4eafa;
.ai-layout {
  border-radius: 30px;
  display: flex;
  height: 100%;
  .buy-key-guide {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
  }
  .preview-section {
    width: 60%;
    height: 100%;
    padding: 30px;
    background-color: $preview-color;
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
    display: flex;
    flex-direction: column;
    .preview-wrapper {
      display: flex;
      flex-direction: column;
      flex: 1;
      .video-wrapper {
        position: relative;
        width: 100%;
        border-radius: 20px;
        background-color: $preview-color;
        overflow: hidden;
        filter: drop-shadow(0px -14px 46px rgba(48, 60, 67, 0.09));
        flex: 1;
        video {
          width: 100%;
          height: 100%;
          min-height: 500px;
        }
        .video-background {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          opacity: 0.2;
          img {
            width: 100%;
            height: 100%;
            max-width: unset !important;
            object-fit: cover;
          }
        }
      }
      .audio-wrapper {
        width: 100%;
        margin-top: 20px;
        audio {
          width: 100%;
        }
      }
    }
    .video-script-wrapper {
      margin-top: 10px;
      height: 300px;
      position: relative;
      .user-credit {
        position: absolute;
        top: 15px;
        right: 10px;
        font-weight: 700;
      }
    }
  }
  .setting-section {
    width: 40%;
    height: 100%;
    padding: 30px;
    background-color: white;
    border-top-right-radius: 15px;
    border-bottom-right-radius: 15px;
  }
}
</style>

<script>
import AiAvatar from './ai-avatar.vue';
import AiBackground from './ai-background.vue';
import AiScript from './ai-script.vue';
import { mapGetters } from 'vuex';
import axios from 'axios';

export default {
  name: 'ai-layout',
  components: {
    AiAvatar,
    AiScript,
    AiBackground
  },
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      video: '',
      audio: '',
      audioType: '',
      script: '',
      pane: 'avatar',
      avatar: {},
      avatars: [],
      title: '',
      avatarSize: 3,
      bg: '#9CB7EC',
      bgType: 'solid',
      noKey: false
    };
  },
  mounted() {
    if (!this.currentUser['deep_api'] || !this.currentUser['deep_secret']) {
      this.noKey = true;
      return;
    }
    const loader = this.$loading.show();
    axios
      .get(`/api/ai-avatars`)
      .then(res => {
        if (res && res.status === 200) {
          res.data.avatars.sample_video_files.forEach(e => {
            this.avatars.push(e);
          });
        } else {
          this.$func.makeToast(this, 'Error', res.data.error, 'danger');
        }
      })
      .catch(() => {
        this.$func.makeToast(this, 'Error', 'Cannot load avatar!', 'danger');
      })
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {
    onNext(info) {
      if (this.pane === 'avatar') {
        this.title = info.title;
        this.avatar = info.avatar;
        this.avatarSize = info.size;
        this.pane = 'background';
      }
    },
    updateSound(info) {
      this.audio = info.path;
      this.audioType = info.type;
      this.$refs.audioRef.load();
    },
    handleBackground(info) {
      this.bg = info.bg;
      this.bgType = info.type;
    },
    onSelectAvatar(info) {
      this.avatar = info;
      this.video = info.video_url;
    },
    handleRender() {
      if (this.avatar === {}) {
        this.$func.showTextMessage(
          'Warning',
          'Select any avatar first!',
          'error'
        );
        return;
      }
      if (this.audio === '') {
        this.$func.showTextMessage(
          'Warning',
          'Please generate sound, record or upload audio!',
          'error'
        );
        return;
      }
      if (this.title === '') {
        this.$func.showTextMessage(
          'Warning',
          'Please enter your title!',
          'error'
        );
        return;
      }
      const loader = this.$loading.show();
      axios
        .post('/api/ai-generateVideo', {
          video: this.avatar.video_url,
          audio: this.audio,
          name: this.title,
          model: this.avatar.name,
          bg: this.bg,
          bgType: this.bgType
        })
        .then(res => {
          if (res && res.status === 200) {
            this.$func.showTextMessage('Notice', res.data.msg, 'info');
            this.$router.push({
              path: '/app/ai-list'
            });
          } else {
            this.$func.showTextMessage('Notice', res.data.error, 'info');
          }
        })
        .catch(() => {
          this.$func.showTextMessage(
            'Error',
            'Cannot generate video right now!',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    }
  }
};
</script>
