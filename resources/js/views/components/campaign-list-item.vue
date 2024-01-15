<template>
  <div
    class="campaign-list-item"
    :class="isActive && 'active'"
    @click="handleClick()"
  >
    <div class="d-flex align-items-center">
      <div class="profile"></div>
      <div class="details">
        <h5>{{ info.title }}</h5>
        <span>{{ info.updated_at }}</span>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.campaign-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  cursor: pointer;
  border-radius: 10px;
  &:hover {
    background-color: #c2e7fd;
    .profile {
      border: 2px solid white;
    }
    .details {
      color: #888;
      span {
        color: #888;
      }
    }
  }
  &.active {
    background-color: #1998e4;
    .profile {
      border: 2px solid white;
    }
    .details {
      color: white;
      span {
        color: white;
      }
    }
  }
  .profile {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    background-color: black;
    justify-content: center;
    color: white;
    cursor: pointer;
  }
  .details {
    margin-left: 20px;
    h5 {
      font-weight: 700;
    }
    span {
      font-size: 0.9rem;
      color: #777;
    }
  }
}
</style>

<script>
export default {
  name: 'campaign-list-item',
  props: {
    info: { type: Object, default: {} },
    isActive: { type: Boolean, default: false }
  },
  data() {
    return {};
  },
  methods: {
    getShorten(name) {
      const rgx = new RegExp(/(\p{L}{1})\p{L}+/, 'gu');
      const initials = [...name.matchAll(rgx)] || [];
      initials = (
        (initials.shift()?.[1] || '') + (initials.pop()?.[1] || '')
      ).toUpperCase();
      return initials;
    },
    handleClick() {
      this.$emit('onClick');
    }
  }
};
</script>
