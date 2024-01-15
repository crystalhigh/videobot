<template>
  <div class="ai-avatar">
    <b-form-group
      label="Video Title"
      label-for="ai-title"
      class="mt-5"
    >
      <b-form-input
        id="ai-title"
        type="text"
        class="borderless-text-field"
        required
        v-model="userTitle"
      ></b-form-input>
    </b-form-group>
    <div class="avatar-section">
      <h5 class="mt-4">
        Select Avatar
      </h5>
      <b-row class="avatar-list">
        <b-col v-for="(itm, idx) in avatars" :key="`avatar-${idx}`" md="4">
          <div
            class="avatar-wrapper"
            :class="userAvatar.name === itm.name ? 'active' : ''"
            @click="handleAvatar(itm)"
          >
            <div class="position-relative w-100">
              <img :src="itm.thumbImage" />
            </div>
            <p class="text-center">{{ getName(itm) }}</p>
            <fa-icon
              :icon="['fas', 'check-circle']"
              class="checked"
              v-if="userAvatar.name === itm.name"
            />
          </div>
        </b-col>
      </b-row>
      <b-row class="mt-5">
        <b-col md="12">
          <div class="size-wrapper">
            <fa-icon :icon="['far', 'image']" class="mr-3" />
            <vue-slider
              v-model="userSize"
              :data="sizes"
              :marks="sizes"
              class="size-slider"
            />
            <fa-icon :icon="['far', 'image']" class="ml-3" />
          </div>
        </b-col>
      </b-row>
    </div>
    <div class="vidpopup-step-button-group mt-5">
      <div>
        <button
          type="button"
          @click="handleNext"
          class="btn circle-button ml-3"
        >
          <fa-icon :icon="['fas', 'arrow-right']" />
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #1998e4;
.avatar-section {
  .avatar-list {
    height: 450px;
    overflow-x: hidden;
    overflow-y: scroll;
    padding-top: 5px;
    padding-bottom: 5px;
  }
  .avatar-wrapper {
    display: flex;
    flex-direction: column;
    cursor: pointer;
    position: relative;
    img {
      width: 100%;
      border-radius: 5px;
      overflow: hidden;
      border: 2px solid transparent;
    }
    .avatar-play {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 2.5rem;
      color: white;
      opacity: 0.85;
      &:hover {
        opacity: 0.5;
      }
    }
    p {
      margin-top: 5px;
      font-weight: 700;
    }
    &.active {
      img {
        border: 2px solid $base-color;
        z-index: 1;
      }
      .checked {
        color: $base-color;
        position: absolute;
        top: -6px;
        right: -6px;
        z-index: 2;
        font-size: 1rem;
        background-color: white;
        border-radius: 50%;
      }
    }
  }
  .size-wrapper {
    display: flex;
    align-items: center;
    padding: 0px 10px;
    svg {
      font-size: 1.2rem;
      color: #a1a1a1;
    }
    .size-slider {
      flex: 1;
    }
  }
}
</style>

<script>
import VueSlider from 'vue-slider-component';
import 'vue-slider-component/theme/default.css';
export default {
  name: 'ai-avatar',
  components: {
    VueSlider
  },
  props: {
    avatar: { type: Object, default: {} },
    avatars: { type: Array, default: [] },
    title: { type: String, default: '' },
    size: { type: Number, default: 3 }
  },
  data() {
    return {
      userTitle: this.title,
      userAvatar: this.avatar,
      userSize: this.size,
      sizes: [1, 2, 3, 4, 5],
      videoSource: [],
      togglerVideo: false
    };
  },
  methods: {
    handleNext() {
      if (this.userTitle === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please input your title!',
          'info'
        );
        return;
      }
      if (!this.userAvatar.name) {
        this.$func.showTextMessage(
          'Notice',
          'Please selct avatar for your video!',
          'info'
        );
        return;
      }
      this.$emit('onNext', {
        avatar: this.userAvatar,
        title: this.userTitle,
        size: this.userSize
      });
    },
    handleAvatar(itm) {
      this.userAvatar = itm;
      this.$emit('onSelectAvatar', itm);
    },
    getName(itm) {
      const name = itm.name.replace(/\.[^/.]+$/, '');
      return name.replace('_', ' ');
    }
  }
};
</script>
