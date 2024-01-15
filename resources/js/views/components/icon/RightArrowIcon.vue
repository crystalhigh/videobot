<template>
  <svg width="14" height="10" viewBox="0 0 31 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 8.5H29M21.5814 1L29 8.5L21.5814 16" :stroke="color" :stroke-width="stroke"/>
            </svg>

</template>

<script>
export default {
  name: 'RightArrowIcon',
  props: {
    color: { type: String, default: '#ffffff' },
    stroke: { type: String, default: "2" },
  }
};
</script>
