<template>
  <div class="ai-script">
    <b-tabs>
      <b-tab title="Text to Speech">
        <b-row>
          <b-col md="7" class="position-relative">
            <b-form-textarea
              class="text-script"
              v-model="text"
              maxlength="1800"
              placeholder="Enter your video script here"
              rows="7"
            ></b-form-textarea>
            <span class="text-stat" v-if="text !== ''"
              >{{ text.length }} / 1800</span
            >
          </b-col>
          <b-col md="5">
            <b-row class="mt-4">
              <b-col
                md="5"
                class="d-flex align-items-center justify-content-end"
              >
                Language
              </b-col>
              <b-col md="7">
                <b-form-select
                  v-model="language"
                  :options="languages"
                  value-field="code"
                  text-field="name"
                ></b-form-select>
              </b-col>
            </b-row>
            <b-row class="mt-4">
              <b-col
                md="5"
                class="d-flex align-items-center justify-content-end"
              >
                Speech
              </b-col>
              <b-col md="7">
                <b-form-select
                  v-model="speech"
                  :options="speeches"
                  value-field="code"
                  text-field="name"
                ></b-form-select>
              </b-col>
            </b-row>
            <b-button
              variant="primary"
              block
              class="mt-4"
              @click="handleGenerate"
              >Preview Voice Over</b-button
            >
          </b-col>
        </b-row>
      </b-tab>
      <b-tab title="Record">
        <div class="voice-record-wrapper">
          <div class="voice-record">
            <audio-recorder @onResult="handleAudio" />
          </div>
        </div>
      </b-tab>
      <b-tab title="Upload">
        <div class="voice-upload-wrapper">
          <div class="voice-uploader" @click="handleBrowse">
            <h4>Choose a file</h4>
          </div>
          <input
            type="file"
            accept=".mp3"
            hidden
            ref="uploadRef"
            @change="uploadFile"
          />
        </div>
      </b-tab>
    </b-tabs>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
$preview-color: #e4eafa;
.ai-script {
  .nav-tabs {
    .nav-link {
      color: #777;
      font-weight: bold;
      &.active,
      &:hover,
      &:focus {
        color: $base-color;
        border-color: $preview-color $preview-color $base-color $preview-color !important;
      }
      &.active {
        border-width: 2px;
        background-color: $preview-color;
      }
    }
  }
  .text-stat {
    position: absolute;
    bottom: 7px;
    right: 35px;
    background: #777;
    color: white;
    padding: 2px 10px;
    font-size: 0.7rem;
    opacity: 0.7;
  }
  .tab-content {
    padding: 30px 0px;
  }
  .text-script {
    padding: 15px;
    border-radius: 10px;
    resize: none;
  }
  .voice-upload-wrapper {
    width: 100%;
    height: 200px;
    padding: 0px 15px;
    .voice-uploader {
      width: 100%;
      height: 100%;
      border: 1px dashed $base-color;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      h4 {
        color: $base-color;
      }
      &:hover {
        background-color: #f4faff6e;
        h4 {
          opacity: 0.75;
        }
      }
    }
  }
  .voice-record-wrapper {
    width: 100%;
    height: 200px;
    padding: 0px 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    .voice-record {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }
  }
}
</style>

<script>
import axios from 'axios';
import AudioRecorder from '@/views/components/audio-recorder';
export default {
  name: 'ai-script',
  components: {
    AudioRecorder
  },
  watch: {
    language: function(newVal, oldVal) {
      if (newVal) {
        const lang = this.languages.find(({ code }) => code === newVal);
        if (lang) {
          this.speeches = lang.speech;
          this.speech = this.speeches[0].code;
        }
      }
    }
  },
  data() {
    return {
      text: '',
      languages: [],
      language: {},
      speeches: [],
      speech: {},
      tts: '',
      file: null
    };
  },
  mounted() {
    axios
      .get('/api/languages/')
      .then(res => {
        if (res && res.status === 200) {
          this.languages = res.data.languages;
          this.language = this.languages[0].code;
        }
      })
      .catch(err => {});
  },
  methods: {
    handleBrowse() {
      this.$refs.uploadRef.click();
    },
    uploadFile(e) {
      if (e.target.files.length === 0) return;
      this.file = e.target.files[0];
      if (!this.file) return;

      const loader = this.$loading.show();

      const formData = new FormData();
      formData.append('file', this.file);
      formData.append('type', 'upload');

      axios
        .post('/api/ai-upload-audio', formData)
        .then(res => {
          if (res && res.status === 200) {
            loader && loader.hide();
            this.$emit('updateSound', {
              path: res.data.file,
              type: 'upload'
            });
            this.$func.makeToast(
              this,
              'Notice',
              'Your audio file is uploaded',
              'success'
            );
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot upload audio file now!',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot upload audio file now!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    onRecord(data) {
      const objUrl = window.URL.createObjectURL(data);
      this.$emit('updateSound', {
        path: objUrl,
        type: 'record'
      });
    },
    handleGenerate() {
      const lang = this.languages.find(item => item.code === this.language);
      const sp = lang.speech.find(item => item.code === this.speech);
      if (this.text === '') {
        this.$func.showTextMessage('Warning', 'Please fill the text!', 'info');
        return;
      }
      const loader = this.$loading.show();
      axios
        .post(`/api/ai-tts`, {
          name: sp.code,
          code: lang.code,
          gender: sp.gender.toUpperCase(),
          text: this.text
        })
        .then(res => {
          if (res && res.status === 200) {
            this.$emit('updateSound', {
              path: res.data.path,
              type: 'tts'
            });
            this.$func.makeToast(
              this,
              'Notice',
              'Voice over is created!',
              'success'
            );
          } else {
            this.$func.makeTaost(this, 'Error', res.data.error, 'error');
          }
        })
        .catch(() => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot generate text right now!',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleAudio(blob) {
      const loader = this.$loading.show();
      const formData = new FormData();
      formData.append('file', blob);
      formData.append('type', 'record');
      axios
        .post('/api/ai-upload-audio', formData)
        .then(res => {
          loader && loader.hide();
          if (res && res.status === 200) {
            this.$emit('updateSound', {
              path: res.data.file,
              type: 'record'
            });
            this.$func.makeToast(
              this,
              'Notice',
              'Your audio file is uploaded',
              'success'
            );
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(() => {
          this.$func.makeToast(this, 'Error', 'Cannot upload audio', 'danger');
        })
        .finally(() => {
          loader && loader.hide();
        });
    }
  }
};
</script>
