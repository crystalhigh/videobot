<template>
  <div
    class="reply-list-item"
    :class="[active ? 'active' : '', read ? 'read' : 'unread']"
    @click="itemClick"
  >
    <div class="reply-content">
      <div class="thumb">
        <img :src="thumb ? getThumbUrl(thumb) : '/images/logo-new1.png'" @error="defaultImage" />
      </div>
      <div class="name">
        <p>{{ name }}</p>
        <p>{{ formatDate() }}</p>
      </div>
    </div>
    <fa-icon :icon="['fas', 'trash']" class="text-danger" @click="itemDelete" />
  </div>
</template>

<style lang="scss" scoped>
$base-color: #3490dc;
.reply-list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  cursor: pointer;
  border-radius: 10px;
  margin-top: 10px;
  &:hover {
    background-color: #e0e2ee;
    color: black;
  }
  &.unread {
    background-color: #bacedf;
    font-weight: 600;
    color: black;
  }
  &.active {
    background-color: #3490dc;
    color: white;
  }
  .reply-dropdown {
    min-width: 30px;
  }
  .reply-content {
    display: flex;
    align-items: center;
    .thumb {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: #546acc;
      overflow: hidden;
      img {
        height: 100%;
        max-width: unset !important;
        object-fit: contain;
        min-width: 100%;
        min-height: 100%;
      }
    }
    .name {
      margin-left: 20px;
      p {
        margin-bottom: 0px;
      }
    }
  }
}
</style>

<script>
export default {
  props: {
    name: { type: String, default: '' },
    date: { type: String, default: '' },
    active: { type: Boolean, default: false },
    thumb: { type: String, default: '' },
    read: { type: Boolean, default: false, },
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
      this.$emit('onClick');
    },
    itemDelete() {
      this.$emit('onDelete');
    },
    formatDate() {
      return this.$func.formatDate(this.date);
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
