<template>
  <div id="page-respond-live">
    <!-- answer is Open Ended -->
    <div v-if="!collect" class="w-100 h-100 center-item">
      <!-- video audio text -->
      <div
        v-if="step && step.answer.type === 0"
        class="w-100 h-100 center-item"
      >
        <div class="open-ended-wrapper">
          <a @click="nextStepByLogo" class="page-respone-live-logo"><img src="/images/logo-new1.png" /></a>
        </div>
        <!-- <div v-if="mode === 'select'" class="w-100">
          <h5 class="text-center">
            {{
              step.answer.content.answerTitle || 'How would you like to answer?'
            }}
          </h5>
          <div class="open-ended-wrapper">
            <button
              type="button"
              class="btn btn-open-ended"
              v-if="step.answer.content.is_audio"
              @click="handleMode('openAudio')"
              :style="{
                color: color,
                backgroundColor: bgColor,
                borderRadius: `${
                  vidpop.advanced.button_radius
                    ? vidpop.advanced.button_radius
                    : 16
                }px`
              }"
            >
              <img src="/images/icons/mic.svg" />
              AUDIO
            </button>
            <button
              type="button"
              class="btn btn-open-ended"
              v-if="step.answer.content.is_video"
              @click="handleMode('openVideo')"
              :style="{
                color: color,
                backgroundColor: bgColor,
                borderRadius: `${
                  vidpop.advanced.button_radius
                    ? vidpop.advanced.button_radius
                    : 16
                }px`
              }"
              :class="vidpop.advanced.oversize ? 'btn-oversize' : ''"
            >
              <img src="/images/icons/camera.svg" />
              VIDEO
            </button>
            <button
              type="button"
              class="btn btn-open-ended"
              v-if="step.answer.content.is_text"
              @click="handleMode('openText')"
              :style="{
                color: color,
                backgroundColor: bgColor,
                borderRadius: `${
                  vidpop.advanced.button_radius
                    ? vidpop.advanced.button_radius
                    : 16
                }px`
              }"
            >
              <img src="/images/icons/text-square.svg" />
              TEXT
            </button>
          </div>
        </div>
        <div v-if="mode === 'openVideo'" class="open-ended-video-wrapper">
          <camera-recorder
            @onRecordSuccess="handleVideo"
            @onBack="mode = 'select'"
          />
        </div>
        <div v-if="mode === 'openAudio'" class="open-ended-audio-wrapper">
          <audio-recorder
            @onResult="handleAudio"
            @onBack="mode = 'select'"
            :bgColor="bgColor"
            :color="color"
          />
        </div>
        <div v-if="mode === 'openText'" class="open-ended-text-wrapper">
          <b-form-textarea
            id="open-end-text"
            v-model="openText"
            placeholder="Type your text here..."
            rows="10"
          ></b-form-textarea>
          <div class="button-group">
            <button
              type="button"
              class="btn btn-answer"
              @click="mode = 'select'"
              :style="{
                color,
                backgroundColor: bgColor
              }"
            >
              Back
            </button>
            <button
              type="button"
              class="btn btn-answer"
              @click="handleText"
              :style="{
                color,
                backgroundColor: bgColor
              }"
            >
              Submit
            </button>
          </div>
        </div> -->
      </div>
      <!-- multiple choice -->
      <div v-if="step && step.answer.type === 1" class="w-100">
        <h6 class="text-center mb-3" v-if="step.answer.option.show_count">
          Choose 1 of {{ this.choices.length }} options
        </h6>
        <div class="multiple-choice-wrapper">
          <button
            type="button"
            class="btn btn-choice"
            v-for="(choice, idx) in choices"
            :key="`choice-${idx}`"
            @click="nextStep(idx)"
          >
            <span
              :style="{
                color: color,
                backgroundColor: bgColor
              }"
              >{{ getLetter(idx) }}</span
            >
            {{ choice.title }}
          </button>
        </div>
      </div>
      <!-- button content -->
      <div v-if="step && step.answer.type === 2" class="w-100">
        <button
          type="button"
          class="btn btn-answer"
          v-if="step.answer.content && (preview || layoutMode)"
          @click="nextStep(0)"
          :style="{
            color: color,
            backgroundColor: bgColor
          }"
        >
          {{ step.answer.content || '' }}
        </button>
        <a
          type="button"
          class="btn btn-answer"
          :href="pv_id && step.answer.option ? getCTALink(step.answer.option.link) : '#'"
          :target="pv_id && step.answer.option ? '_blank' : ''"
          @click="nextStep(0)"
          v-if="!preview && !layoutMode && step.answer.content"
          :style="{
            color: color,
            backgroundColor: bgColor
          }"
        >
          {{ step.answer.content || '' }}
        </a>
      </div>
      <!-- calendly -->
      <div v-if="step && step.answer.type === 3" class="w-100">
        <vue-calendly
          :url="step.answer.content"
          v-if="step.answer.content"
        ></vue-calendly>
        <button
          type="button"
          class="btn btn-answer mt-3"
          @click="nextStep(0)"
          :style="{
            color: color,
            backgroundColor: bgColor
          }"
        >
          Next
        </button>
      </div>
      <!-- payment -->
      <div v-if="step && step.answer.type === 4" class="w-100">
        <h5 class="text-center mb-3">
          Amount: ${{ parseFloat(step.answer.option.value).toFixed(2) }}
        </h5>
        <button
          type="button"
          class="btn btn-answer"
          v-if="preview || layoutMode"
          @click="nextStep(0)"
          :style="{
            color: color,
            backgroundColor: bgColor
          }"
        >
          {{ step.answer.option.text || 'Pay It' }}
        </button>
        <a
          type="button"
          class="btn btn-answer"
          :href="step.answer.content"
          target="_blank"
          @click="nextStep(0)"
          v-else
          :style="{
            color: color,
            backgroundColor: bgColor
          }"
        >
          {{ step.answer.option.text || 'Pay It' }}
        </a>
      </div>
    </div>
    <div v-else class="collect-data-wrapper">
      <div class="collect-data-wrapper-content">
        <h5>
          One more step: save your information in below form to reach out to
          you!
        </h5>
        <b-form-group
          id="collect-first-name-group"
          label="*First name:"
          label-for="collect-first-name"
          class="mt-4"
        >
          <b-form-input
            id="collect-first-name"
            type="text"
            required
            v-model="collect_first_name"
          ></b-form-input>
        </b-form-group>
        <b-form-group
          id="collect-last-name-group"
          label="*Last name:"
          label-for="collect-last-name"
          class="mt-4"
        >
          <b-form-input
            id="collect-last-name"
            type="text"
            required
            v-model="collect_last_name"
          ></b-form-input>
        </b-form-group>
        <b-form-group
          id="collect-email-group"
          label="*Your email:"
          label-for="collect-email"
          class="mt-4"
        >
          <b-form-input
            id="collect-email"
            type="email"
            required
            v-model="collect_email"
          ></b-form-input>
        </b-form-group>
        <b-form-group
          id="collect-street-country-group"
          label="Your Country:"
          class="mt-3"
        >
          <b-form-input
            id="collect-country"
            type="text"
            disabled
            value="United States"
          ></b-form-input>
        </b-form-group>
        <b-form-group
          id="collect-street-address-group"
          label="*Street Address:"
          class="mt-4"
        >
          <autocomplete :suggestions="streetSuggestions" :fetchSuggestions="fetchStreetSuggestions"/>
        </b-form-group>
        <!-- <b-form-group
          id="collect-zip-code-group"
          label="*Zip Code:"
          label-for="collect-zip-code"
          class="mt-4"
        >
          <b-form-input
            id="collect-zip-code"
            type="text"
            required
            v-model="collect_zip_code"
          ></b-form-input>
        </b-form-group> -->
        <b-form-group
          id="collect-phone-group"
          label="*Phone:"
          label-for="collect-phone"
          class="mt-4"
        >
          <b-form-input
            id="collect-phone-desktop"
            type="text"
            required
            v-model="collect_phone"
            placeholder="(+1) 000 000 0000"
            v-mask="['(+1) ### ### ####']"
          ></b-form-input>
        </b-form-group>
        <b-form-group class="mt-4">
          <b-form-checkbox v-model="conditionCheck" size="lg">
            <span class="agree-terms">
              By ticking, you are confirming that you have read, understood and agree to our
              <a href="/terms" target="_blank">terms</a> and <a href="/privacy" target="_blank">privacy.</a>
            </span>
          </b-form-checkbox>
        </b-form-group>
      </div>
      <button
        type="button"
        class="btn btn-answer"
        @click="handleSubmit"
        :style="{
          color: color,
          backgroundColor: bgColor
        }"
      >
        Next
      </button>
    </div>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
#page-respond-live {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px;
  .page-respone-live-logo {
    width: 80px;
    &:hover {
      cursor: pointer;
      filter: brightness(110%);
    }
  }
  #collect-street-address-group {
    position: relative;
  }
  #collect-street-country-group {
    position: relative;
  }
  .open-ended-wrapper {
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
    .btn-open-ended {
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: $base-color;
      color: white;
      padding: 12px;
      border-radius: 15px;
      transition: 0.4s;
      font-size: 0.7rem;
      margin: 8px;
      &:hover {
        filter: brightness(120%);
      }
      img {
        width: 15px;
        height: 15px;
        margin-bottom: 10px;
        filter: invert(1);
      }
    }
    .btn-oversize {
      transform: scale(1.2);
    }
  }
  .open-ended-text-wrapper {
    width: 100%;
    #open-end-text {
      background-color: #464242c7;
      color: white;
      padding: 1rem;
      resize: none;
      border: none;
      box-shadow: none;
      margin-bottom: 50px;
      font-size: 1.1rem;
    }
    .button-group {
      display: flex;
      justify-content: space-between;
      .btn-answer {
        margin-right: 10px;
        margin-left: 10px;
      }
    }
  }

  .open-ended-video-wrapper {
    position: relative;
    height: 100%;
  }

  .open-ended-audio-wrapper {
    position: relative;
    height: 100%;
  }

  .multiple-choice-wrapper {
    display: flex;
    flex-direction: column;
    .btn-choice {
      width: 100%;
      background-color: #0000000d;
      text-align: left;
      font-weight: 700;
      margin-bottom: 15px;
      border-radius: 30px;
      padding: 10px 30px;
      display: flex;
      align-items: center;
      font-size: 0.7rem;
      span {
        border-radius: 50%;
        background-color: $base-color;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px;
        width: 30px;
        height: 30px;
        margin-right: 10px;
      }
      &:hover {
        border: 1px solid $base-color;
      }
      @media (max-width: 1500px) {
        padding: 5px 15px;
      }
    }
  }
  .btn-answer {
    width: 100%;
    background-color: $base-color;
    color: white;
    padding: 10px 30px;
    box-shadow: rgb(0 0 0 / 10%) 0px 10px 30px 0px;
    font-weight: 700;
    font-size: 1.2rem;
    &:hover {
      filter: brightness(110%);
    }
  }
  .collect-data-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    padding-top: 50px;
    padding-bottom: 20px;
    h5 {
      font-size: 1rem;
      color: black;
    }
    .form-group label {
      color: black;
      margin-bottom: 0px;
    }
    .form-control {
      color: black;
      border-radius: 20px;
      &:focus {
        color: black !important;
      }
    }
  }
  .collect-data-wrapper-content {
    overflow-y: auto;
    background-color: white;
    color: black;
    padding: 20px;
  }
}
</style>

<script>
import CameraRecorder from '@/views/components/camera-recorder';
import AudioRecorder from '@/views/components/audio-recorder';
import autocomplete from '@/views/components/autocomplete';
import {mask} from 'vue-the-mask'
export default {
  name: 'respond-desktop',
  components: {
    CameraRecorder,
    AudioRecorder,
    autocomplete
  },
  directives: {mask},
  watch: {
    step: function(newVal, oldVal) {
      this.step = newVal;
      if (this.step.answer.type === 1) {
        this.choices = this.step.answer.content;
      }
    }
  },
  props: {
    step: { type: Object, default: {} },
    vidpop: { type: Object, default: {} },
    // publisher: { type: Object, default: {} },
    pv_id: { type: Number, default: 0 },
    preview: { type: Boolean, default: true },
    collected: { type: Boolean, default: false },
    layoutMode: { type: Boolean, default: true },
    color: { type: String, default: '#ffffff' },
    bgColor: { type: String, default: '#1998e4' }
  },
  data() {
    return {
      choices: [],
      collect_first_name: '',
      collect_last_name: '',
      collect_email: '',
      collect_street_address: '',
      // collect_zip_code: '',
      collect_phone: '',
      collect: false,
      selectedIdx: '',
      mode: 'select',
      openText: '',
      conditionCheck: false,
      streetSuggestions: [],
    };
  },
  mounted() {
    if (this.step.answer.type === 1) {
      this.choices = this.step.answer.content;
    }
  },
  methods: {
    getCTALink(link) {
      return link.replace('$publisher_id$', this.pv_id);
    },
    nextStepByLogo() {
      this.nextStep(0);
    },
    fetchStreetSuggestions(suggestion) {
      // smarty.com (needs embeded key & subscription-international address autocomplete)
      this.streetSuggestions = [];
      this.collect_street_address = suggestion;
      if (this.collect_street_address.replaceAll(' ', '') != '') {
        this.isSelectedStreet = false;
        axios.get('https://us-autocomplete-pro.api.smarty.com/lookup', {
          params: {
            // key: '185801865482640344', // localhost
            key: '185801865289525135', // server (vid-gen.com)
            search: this.collect_street_address,
            license: 'us-autocomplete-pro-cloud',
          }
        })
        .then(res => {
          res.data.suggestions.map((val, index) => {
            this.streetSuggestions = [...this.streetSuggestions, {
              address_id: `${val.street_line} ${val.secondary} ${val.city} ${val.state} ${index}`,
              address_text: `${val.street_line} ${val.secondary} ${val.city} ${val.state}`
            }];
          });
        })
        .catch(err => {
          console.log('err =>', err);
        })
      } else {
        this.streetSuggestions = [];
      }
    },
    getLetter(idx) {
      return String.fromCharCode(65 + idx);
    },
    nextStep(idx) {
      if (this.layoutMode) {
        return;
      }
      if (!this.collected && this.step.data_collection) {
        this.selectedIdx = idx;
        this.collect = true;
        return;
      }
      this.openText = '';
      this.mode = 'select';
      this.$emit('onNext', idx);
    },
    handleSubmit() {
      if (
        this.collect_first_name === '' ||
        this.collect_first_name === '' ||
        this.collect_email === '' ||
        this.collect_street_address === '' ||
        // this.collect_zip_code === '' ||
        this.collect_phone === ''
      ) {
        this.$func.showTextMessage('Notice', 'Please fill the input!', 'error');
        return;
      }
      if (!this.$func.validateEmail(this.collect_email)) {
        this.$func.showTextMessage(
          'Notice',
          'Please input valid email!',
          'error'
        );
        return;
      }
      // let arrStreet = this.streetSuggestions.filter(val => val.address_text == this.collect_street_address)
      // if (this.streetSuggestions.length == 0 || arrStreet.length == 0) {
      //   this.$func.showTextMessage(
      //     'Notice',
      //     'Your street address is invalid!',
      //     'warning'
      //   );
      //   return;
      // }
      if (!this.conditionCheck) {
        this.$func.showTextMessage(
          'Notice',
          'Please check our terms and conditions!',
          'warning'
        );
        return;
      }
      let regex = /^\(\+1\) \d{3} \d{3} \d{4}$/;
			if (!regex.test(this.collect_phone)) {
				this.$func.makeToast(
          this,
          'Warning',
          'Phone number should be 10 digits!',
          'danger'
        );
				return;
			}
      regex = /^[a-zA-Z\s]+$/;
      if (!regex.test(this.collect_first_name) || !regex.test(this.collect_last_name)) {
				this.$func.makeToast(
          this,
          'Warning',
          'Only alphabetic characters are allowed in Name!',
          'danger'
        );
				return;
			}
      this.collect = false;
      this.openText = '';
      this.mode = 'select';
      this.$emit('onCollect', {
        first_name: this.collect_first_name,
        last_name: this.collect_last_name,
        email: this.collect_email,
        street: this.collect_street_address,
        // zipcode: this.collect_zip_code,
        phone: this.collect_phone,
        idx: this.selectedIdx
      });
    },
    handleText() {
      if (this.openText === '') {
        this.$func.showTextMessage('Notice', 'Please fill the input!', 'error');
        return;
      }
      this.$emit('onEndText', this.openText);
      this.nextStep(0);
    },
    handleVideo(info) {
      const loader = this.$loading.show();
      const formData = new FormData();
      formData.append('file', info.data);
      formData.append('fileType', 'camera');
      formData.append('vid', this.vidpop.id);
      axios
        .post('/api/upload-file', formData)
        .then(res => {
          loader && loader.hide();
          if (res && res.status === 200) {
            if (window.stream) {
              window.stream.getTracks().forEach(function(track) {
                track.stop();
              });
            }
            this.$emit('onEndVideo', {
              path: res.data.file
            });
            this.nextStep(0);
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(err => {
          this.$func.makeToast(this, 'Error', 'Cannot upload video', 'danger');
        });
    },
    handleAudio(blob) {
      const loader = this.$loading.show();
      const formData = new FormData();
      formData.append('file', blob);
      formData.append('vid', this.vidpop.id);
      axios
        .post('/api/upload-audio', formData)
        .then(res => {
          loader && loader.hide();
          if (res && res.status === 200) {
            this.$emit('onEndAudio', {
              path: res.data.file
            });
            this.nextStep(0);
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(() => {
          this.$func.makeToast(this, 'Error', 'Cannot upload audio', 'danger');
        });
    },
    handleMode(mode) {
      if (this.layoutMode) {
        return;
      }
      this.mode = mode;
    }
  }
};
</script>
