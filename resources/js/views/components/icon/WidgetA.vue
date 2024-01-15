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
    <rect
      x="33"
      y="34"
      width="33"
      height="33"
      rx="16.5"
      fill="#1998E4"
      fill-opacity="0.2"
    />
    <rect
      x="36"
      y="37"
      width="27"
      height="27"
      rx="13.5"
      fill="white"
      stroke="#1998E4"
      stroke-width="2"
    />
    <path
      d="M49.5 12V28.5"
      :stroke="selected ? 'white' : '#1998E4'"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      d="M45 24L49.5 28.5L54 24"
      :stroke="selected ? 'white' : '#1998E4'"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>

<script>
export default {
  name: 'WidgetA',
  props: {
    selected: { type: Boolean, default: false }
  },
  data() {
    return {};
  }
};
</script>
