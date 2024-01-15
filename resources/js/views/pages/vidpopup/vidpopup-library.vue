<template>
  <div id="page-vidpopup-library">
    <div class="video-section">
      <video
        autoplay
        loop
        :key="videoSource"
        ref="videoRef"
        muted
        @click="handlePlay(0)"
      >
        <source :src="getUrl()" :type="getType(videoSource)" />
      </video>
      <span
        class="overlay-play"
        v-if="!playing && videoSource"
        @click="handlePlay(1)"
      >
        <fa-icon :icon="['fas', 'play']" />
      </span>
    </div>
    <div class="library-section">
      <div class="library-wrapper">
        <b-tabs>
          <b-tab title="My Videos" active @click="setActive('Videos')">
            <div v-if="activeTab === 'Videos'">
              <uploads-pane @onSelect="handleSelect" />
            </div>
          </b-tab>
          <b-tab title="Pixabay" @click="setActive('Pixabay')">
            <div v-if="activeTab === 'Pixabay'">
              <pixabay-pane @onSelect="handleSelect" />
            </div>
          </b-tab>
          <b-tab title="Pexels" @click="setActive('Pixels')">
            <div v-if="activeTab === 'Pixels'">
              <pixels-pane @onSelect="handleSelect" />
            </div>
          </b-tab>
          <b-tab title="Giphy" @click="setActive('Giphy')">
            <div v-if="activeTab === 'Giphy'">
              <giphy-pane @onSelect="handleSelect" />
            </div>
          </b-tab>
        </b-tabs>
      </div>
      <div class="d-flex justify-content-end mb-4 px-5">
        <button type="button" class="btn btn-back" @click="handleBack">
          <fa-icon :icon="['fas', 'arrow-left']" />
        </button>
        <button type="button" class="btn btn-continue" @click="handleContinue">
          Continue
          <span class="btn-fa-continue">
            <fa-icon :icon="['fas', 'arrow-right']" />
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
#page-vidpopup-library {
  height: 100%;
  border-radius: 30px;
  overflow: hidden;
  display: flex;
  .video-section {
    background-color: #b2dcf5;
    height: 100%;
    width: 50%;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    height: calc(100vh - 40px);
    video {
      width: 100%;
      height: 100%;
    }
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
      &:hover {
        cursor: pointer;
        width: 82px;
        height: 82px;
        font-size: 2.1rem;
      }
    }
  }
  .library-section {
    width: 50%;
    height: 100%;
    background-color: white;
    display: flex;
    flex-direction: column;
    .library-wrapper {
      flex: 1;
      padding: 30px 30px 0px 30px;

      .nav-tabs {
        .nav-link {
          color: #777;
          font-weight: bold;
          &.active,
          &:hover,
          &:focus {
            color: $base-color;
            border-color: #ffffff #ffffff $base-color #ffffff !important;
          }
          &.active {
            border-width: 2px;
          }
        }
      }
    }
    .btn-back {
      background-color: #f0f1f3;
      color: black;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      &:hover {
        opacity: 0.75;
      }
    }
    .btn-continue {
      background-color: #1998e4;
      color: white;
      font-size: 1rem;
      border-radius: 30px;
      padding: 5px 30px;
      padding-right: 15px;
      display: flex;
      align-items: center;
      margin-left: 15px;
      .btn-fa-continue {
        margin-left: 10px;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        color: #1998e4;
        border-radius: 50%;
        font-size: 0.8rem;
      }
      &:hover {
        opacity: 0.75;
      }
    }
  }
}
</style>

<script>
import PixabayPane from '@/views/components/pixabay-pane';
import PixelsPane from '@/views/components/pixels-pane';
import UploadsPane from '@/views/components/uploads-pane';
import GiphyPane from '@/views/components/giphy-pane';
import axios from 'axios';
import { mapGetters } from 'vuex';
export default {
  name: 'vidpopup-library',
  components: {
    UploadsPane,
    PixabayPane,
    PixelsPane,
    GiphyPane
  },
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      videoSource: '',
      videoType: '',
      videoThumb: '',
      activeTab: 'Videos',
      id: '',
      step_id: '',
      index: -1,
      playing: false,
      awsUrl: ''
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
  watch: {
    // videoSource: function(newVal, oldVal) {
    //   this.$refs.videoRef.play();
    // }
  },
  mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.index = this.$route.query.index;
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    setActive(pane) {
      this.activeTab = pane;
    },
    handleSelect(info) {
      this.videoSource = info.video;
      this.videoType = info.type;
      this.videoThumb = info.thumb;
      this.playing = false;
    },
    handleContinue() {
      if (this.videoSource === '') {
        // show toast
        this.$func.showTextMessage(
          'Notice',
          'Please select any video!',
          'info'
        );
        return;
      }
      const loader = this.$loading.show();
      axios
        .post(`/api/create-step-library`, {
          url: this.videoSource,
          type: this.videoType,
          thumb: this.videoThumb,
          vid: this.id,
          sid: this.step_id,
          index: this.index
        })
        .then(res => {
          if (res && res.status === 201) {
            // step created
            loader && loader.hide();
            this.$router.push({
              path: `/app/vidgen/${this.id}/${res.data.id}/edit/overlay`
            });
          } else {
            this.$func.showTextMessage('Error', res.data.error, 'error');
          }
        })
        .catch(err => {
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
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/manage?index=${this.index}`
      });
    },
    handlePlay(status) {
      if (status === 1) {
        this.playing = true;
        this.$refs.videoRef.muted = false;
        this.$refs.videoRef.play();
      } else {
        this.playing = false;
        this.$refs.videoRef.pause();
      }
    },
    getType(url) {
      if (url.endsWith('webm')) {
        return 'video/webm';
      }
      return 'video/mp4';
    },
    getUrl() {
      if (this.videoType === 'uploads') {
        return `${this.awsUrl}${this.videoSource}`;
      }
      return this.videoSource;
    }
  }
};
</script>
