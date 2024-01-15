<template>
  <div id="page-endscreen">
    <div
      class="endscreen-wrapper"
      :style="{
        backgroundColor: vidpop.end_bg,
        color: vidpop.end_color
      }"
    >
      <div
        class="overlay-text"
        :style="{
          top: getPosition(vidpop.end_position),
          transform:
            Number(vidpop.end_position) === 1 ? 'translateY(-50%)' : 'unset'
        }"
      >
        <h1
          class="mb-3"
          :style="{
            fontSize: vidpop.title_size
          }"
        >
          {{ vidpop.title }}
        </h1>
        <h3
          v-html="linebreak(vidpop.content)"
          :style="{
            fontSize: vidpop.content_size
          }"
        ></h3>
        <div
          class="w-100 text-center"
          v-if="vidpop.is_custom && vidpop.custom_text"
        >
          <a
            :href="vidpop.custom_link"
            target="_blank"
            class="btn"
            :style="{
              color: vidpop.custom_color,
              backgroundColor: vidpop.custom_bgcolor
            }"
          >
            {{ vidpop.custom_text }}
          </a>
        </div>
      </div>
      <div class="brand-wrapper d-flex align-items-center justify-content-center" :style="{ backgroundColor: bgColor }">
        <a class="brand-wrapper-sponsored-a" @click="onSponsored"><span class="brand-wrapper-sponsored text-white d-flex align-items-between">Sponsored&nbsp;<fa-icon :icon="['fas', 'exclamation-circle']" /></span></a>
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
    <b-modal
      id="sponsored-modal1"
      hide-footer
      title="Sponsored"
      centered
      ref="sponsoredModal1"
      size="md">
      <h6>You are seeing this ad because you visited a vetted publisher of the vid-GEN ad network.</h6>
      <h6>vid-GEN does not have your data.</h6>
      <h6>Please contact the publisher to learn more about your privacy. </h6><br/>
    </b-modal>
  </div>
</template>

<style lang="scss" scoped>
#page-endscreen {
  .endscreen-wrapper {
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
    position: relative;
    text-align: center;
    .brand-wrapper-sponsored-a {
      &:hover {
        cursor: pointer;
      }
    }
    .brand-wrapper-sponsored {
      font-weight: 800;
      font-size: 12px;
    }
    .overlay-text {
      position: absolute;
      text-align: center;
      width: 100%;
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
</style>

<script>
export default {
  name: 'endscreen',
  data() {
    return {};
  },
  props: {
    vidpop: { type: Object, default: {} },
    brand: { type: Object, default: {} },
    bgColor: { type: String, default: '#1998e4' },
    color: { type: String, default: '#ffffff' }
  },
  methods: {
    onSponsored() {
      this.$bvModal.show('sponsored-modal1');
    },
    getPosition(pos) {
      if (Number(pos) === 0) {
        return '20%';
      } else if (Number(pos) === 1) {
        return '45%';
      } else if (Number(pos) === 2) {
        return '70%';
      }
    },
    linebreak(content) {
      if (content) {
        return content.replace(/\n/g, '<br />');
      }
      return '';
    }
  }
};
</script>
