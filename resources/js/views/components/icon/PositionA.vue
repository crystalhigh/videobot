<template>
  <svg
    width="75"
    height="75"
    viewBox="0 0 75 75"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <rect
      width="75"
      height="75"
      rx="20"
      :fill="selected ? '#1998E4' : '#E3E3E3'"
    />
    <path
      d="M15.8594 16.8584L37.3423 38.3413"
      :stroke="selected ? 'white' : '#1998E4'"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      d="M25.623 38.3408H37.341V26.6229"
      :stroke="selected ? 'white' : '#1998E4'"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <rect
      x="41"
      y="42"
      width="25"
      height="25"
      rx="12.5"
      fill="#1998E4"
      fill-opacity="0.2"
    />
    <rect
      x="44"
      y="45"
      width="19"
      height="19"
      rx="9.5"
      fill="white"
      stroke="#1998E4"
      stroke-width="2"
    />
  </svg>
</template>

<script>
export default {
  name: 'PositionA',
  props: {
    selected: { type: Boolean, default: '' }
  },
  data() {
    return {};
  }
};
</script>
