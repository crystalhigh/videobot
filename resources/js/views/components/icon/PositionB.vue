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
      :fill="selected ? '#1998e4' : '#E3E3E3'"
    />
    <path
      d="M58.3906 16.6543L37.6543 37.3906"
      :stroke="selected ? 'white' : '#1998E4'"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      d="M37.6562 26.0801V37.3908H48.967"
      :stroke="selected ? 'white' : '#1998E4'"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <rect
      x="11"
      y="43"
      width="23"
      height="23"
      rx="11.5"
      fill="#1998E4"
      fill-opacity="0.2"
    />
    <rect
      x="14"
      y="46"
      width="17"
      height="17"
      rx="8.5"
      fill="white"
      stroke="#1998E4"
      stroke-width="2"
    />
  </svg>
</template>

<script>
export default {
  name: 'PositionB',
  props: {
    selected: { type: Boolean, default: '' }
  },
  data() {
    return {};
  }
};
</script>
