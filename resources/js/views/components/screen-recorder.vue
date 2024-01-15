<template>
  <div class="screen-recorder">
    <div class="video-wrapper" :class="mobile ? 'mobile' : ''">
      <!-- <canvas id="screen-rec"></canvas> -->
      <video id="cam-rec" playsinline autoplay loop muted></video>

      <!-- start recording -->
      <div class="action-wrapper" v-if="!recordStarted && !recorded">
        <div class="rec-label">
          Hit <span class="text-danger font-bold">RECORD</span> to Start!
        </div>
        <div class="center-item">
          <div
            class="btn-rec-circle large recorder"
            @click="countdown = true"
          ></div>
          <div class="btn-rec-circle close" @click="backVideo">
            <fa-icon :icon="['fas', 'times']" class="text-white" />
          </div>
        </div>
      </div>

      <!-- recording -->
      <div class="action-wrapper" v-if="recordStarted && !recorded">
        <div class="rec-label">
          Now Recording!
        </div>
        <div class="btn-rec-circle large stop-rec" @click="stopRecording">
          <div class="rect"></div>
        </div>
      </div>

      <!-- after recording -->
      <div class="action-wrapper" v-if="recorded">
        <div class="rec-label">Like it?</div>
        <div class="d-flex flex-row">
          <div class="btn-rec-circle large approve" @click="approve">
            Yes
          </div>
          <div class="btn-rec-circle large reject ml-3" @click="reset">
            No
          </div>
        </div>
      </div>
    </div>
    <countdown @done="startRecord" v-if="countdown" />
  </div>
</template>

<style lang="scss" scoped>
.screen-recorder {
  height: 100%;
  overflow: hidden;
  .video-wrapper {
    display: flex;
    width: 100%;
    border-radius: 20px;
    height: 100%;
    position: relative;
    video {
      width: 100%;
      border-radius: 20px;
      background-color: black;
    }
    &.mobile video {
      border-radius: 0px;
    }
    .action-wrapper {
      position: absolute;
      bottom: 50px;
      left: 0px;
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      .btn-rec-circle {
        transition: all 0.3s;
        cursor: pointer;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        &.large {
          width: 72px;
          height: 72px;
        }
        &:hover {
          transform: scale(1.1);
        }
      }
      .rec-label {
        font-weight: 500;
        font-size: 1.2rem;
        background-color: #00000093;
        color: white;
        margin-bottom: 24px;
        text-align: center;
        padding: 5px 20px;
        .font-bold {
          font-weight: bold;
        }
      }
      .recorder {
        background-color: #ff3c4ccc;
        &:hover {
          background-color: #ff3c4c;
        }
      }
      .close {
        background-color: #00000080;
        margin-left: 20px;
      }
      .approve {
        background-color: #199ae4d0;
        color: white;
      }
      .reject {
        background-color: #ff3c4cc9;
        color: #aaa;
      }
      .stop-rec {
        background-color: #cccccc66;
        .rect {
          border-radius: 6px;
          width: 36px;
          height: 36px;
          background-color: #e3491ce6;
        }
      }
    }
  }
}
</style>

<script>
import Countdown from '@/views/components/countdown';
export default {
  name: 'screen-recorder',
  components: {
    Countdown
  },
  props: {
    mobile: { type: Boolean, default: false }
  },
  data() {
    return {
      recordStarted: false,
      recorded: false,
      countdown: false,
      mediaRecorder: null,
      recordedBlobs: [],
      bufferedBlob: [],
      audioId: '',
      noAudio: false
    };
  },
  async mounted() {
    await this.init();
  },
  methods: {
    async init(initial = true) {
      const loader = this.$loading.show();
      let stream;
      let displayStream = null;
      let voiceStream = null;
      try {
        displayStream = await navigator.mediaDevices.getDisplayMedia({
          video: true,
          audio: false
        });
      } catch (displayException) {
        this.$func.showHtmlMessage(
          'Error',
          '<h4>You have no permission for camera!</h4><p><span style="color: red;margin-right:3px;">Important:</span>To record video, allow access to camera & microphone when your browser prompts.<br /><a href="https://help.vid-gen.com/knowledge-base/1-allow-permission-for-camera-and-mic" target="_blank" style="margin-right: 5px;">Facing problems?&nbsp;&nbsp;Know How to Fix.</a></p>',
          'error'
        );
        loader && loader.hide();
        this.backVideo();
        return;
      }

      try {
        voiceStream = await navigator.mediaDevices.getUserMedia({
          audio: true,
          video: false
        });
        this.noAudio = false;
      } catch (audioException) {
        this.noAudio = true;
        voiceStream = null;
      }

      let tracks;
      if (voiceStream) {
        tracks = [
          ...displayStream.getTracks(),
          ...voiceStream.getAudioTracks()
        ];
      } else {
        tracks = [...displayStream.getTracks()];
      }
      stream = new MediaStream(tracks);
      window.stream = stream;
      loader && loader.hide();
    },
    backVideo() {
      this.$emit('onBack');
    },
    handleDataAvailable(event) {
      if (event.data && event.data.size > 0) {
        this.recordedBlobs.push(event.data);
      }
    },
    startRecord() {
      this.countdown = false;
      this.recordedBlobs = [];
      const mimeType = 'video/webm;codecs;vp8,opus';
      const options = { mimeType };
      try {
        this.mediaRecorder = new MediaRecorder(window.stream, options);
      } catch (e) {
        this.$func.showTextMessage(
          'Error',
          'Something went wrong while recording!',
          'error'
        );
      }

      this.mediaRecorder.onstop = event => {
        this.bufferedBlob = new Blob(this.recordedBlobs, {
          type: mimeType
        });
        const videoWrapper = document.querySelector('video#cam-rec');
        videoWrapper.srcObject = null;
        videoWrapper.src = window.URL.createObjectURL(this.bufferedBlob);
        this.recorded = true;
        videoWrapper.muted = false;
      };

      this.mediaRecorder.ondataavailable = this.handleDataAvailable;
      this.mediaRecorder.start();

      this.recordStarted = true;
    },
    stopRecording() {
      this.mediaRecorder.stop();
    },
    approve() {
      this.$emit('onRecordSuccess', {
        data: this.bufferedBlob
      });
    },
    async reset() {
      this.recorded = false;
      this.recordStarted = false;
      this.bufferedBlob = [];
      this.recordedBlobs = [];
      await this.init(false);
    }
  }
};
</script>
