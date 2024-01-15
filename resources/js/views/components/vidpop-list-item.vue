<template>
  <div
    class="vidpop-list-item"
    :class="info.active ? 'active' : ''"
    @click="itemClick"
  >
    <div class="vidpop-content">
      <div class="thumb">
        <img
          :src="info.thumb ? getThumbUrl(info.thumb) : ''"
          @error="defaultImage"
        />
      </div>
      <div class="name">
        <p>
          <b>{{ info.name }}</b>
        </p>
        <p>
          ${{ info.cost }} / lead
        </p>
        <p>{{ formatDate() }}</p>
      </div>
      <span class="badge-admin" v-if="info.is_template">
        Template
      </span>
    </div>

  </div>
</template>

<style lang="scss" scoped>
$base-color: #3490dc;
.vidpop-list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  cursor: pointer;
  border-radius: 10px;
  margin-top: 10px;
  width: 100%;
  &:hover {
    background-color: #e0e2ee;
    color: black;
  }
  &.active {
    background-color: #3490dc;
    color: white;
  }
  .vidpop-dropdown {
    min-width: 30px;
  }
  .vidpop-content {
    display: flex;
    align-items: center;
    position: relative;
    width: 100%;
    .thumb {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: #546acc;
      overflow: hidden;
      img {
        width: auto;
        height: 100%;
        max-width: unset !important;
        object-fit: cover;
        min-height: 100%;
        min-width: 100%;
      }
    }
    .name {
      margin-left: 20px;
      flex: 1;
      p {
        margin-bottom: 0px;
      }
    }
    .badge-admin {
      position: absolute;
      font-size: 10px;
      background-color: #6a5df1;
      color: white;
      top: -3px;
      right: 0px;
      border-radius: 30px;
      padding: 3px 7px;
      font-weight: 700;
    }
  }
}
</style>

<script>
export default {
  props: {
    info: { Object, default: {} },
    index: { Number, default: 0 },
  },
  data() {
    return {
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    itemClick() {
      event.preventDefault();
      event.stopPropagation();
      this.$emit('itemClick', this.info, this.index);
    },
    formatDate() {
      return this.$func.formatDate(this.info.created_at);
    },
    defaultImage(e) {
      e.target.src = '/images/placeholder.png';
    },
    handleUsers() {},
    getThumbUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
