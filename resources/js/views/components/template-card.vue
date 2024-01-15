<template>
  <div class="template-card">
    <div class="template-img-wrapper">
      <img :src="getThumbUrl(info.thumb)" @error="defaultImage" />
    </div>
    <div class="px-1">
      <h5 class="mt-4">{{ info.name }}</h5>
      <p class="mt-2 template-description px-2" v-if="description">
        {{ description }}
      </p>
      <div class="template-buttons">
        <button type="button" class="btn btn-choose" @click="handleChoose">
          <img src="/images/icons/select.svg" class="mr-1" />
          Choose
        </button>
        <a
          :href="`https://vid-gen.com/live/${info.code}`"
          class="btn btn-choose"
          target="_blank"
        >
          <img
            src="/images/icons/preview.svg"
            class="mr-1"
            style="margin-bottom: 1px;"
          />
          Preview
        </a>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #1998e4;
.template-card {
  padding: 20px 0px 40px 0px;
  text-align: center;
  box-shadow: 0px 5px 40px rgba(0, 0, 0, 10%);
  border-radius: 5px;
  height: 100%;
  .template-img-wrapper {
    width: 100%;
    max-height: 200px;
    background-color: black;
    overflow: hidden;
    img {
      width: 100%;
    }
  }
  .template-buttons {
    display: flex;
    justify-content: space-evenly;
    .btn-choose {
      background-color: $base-color;
      color: white;
      text-transform: uppercase;
      padding: 5px 15px;
      display: flex;
      align-items: center;
      font-size: 0.75rem;
      &:hover {
        filter: brightness(110%);
      }
      img {
        width: 20px;
      }
    }
  }
}
</style>

<script>
export default {
  props: {
    info: { type: Object, default: {} }
  },
  data() {
    return {
      awsUrl: 'https://vidpopup.s3.amazonaws.com/',
      description: ''
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
    const social = this.info.social ? JSON.parse(this.info.social) : {};
    if (social.description) {
      this.description = social.description;
    }
  },
  methods: {
    handleChoose() {
      this.$emit('onChoose');
    },
    defaultImage(e) {
      e.target.src = '/images/placeholder.png';
    },
    getThumbUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
