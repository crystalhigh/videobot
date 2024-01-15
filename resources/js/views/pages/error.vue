<template>
  <div id="page-error">
    <div class="content">
      <div>
        <img src="/images/logo-new.png" />
      </div>
      <h4>We can't find this VidGen, try another one!</h4>
      <router-link to="/" tag="span" class="go-home">
        Home
      </router-link>
    </div>
  </div>
</template>
<style lang="scss" scoped>
#page-error {
  width: 100%;
  min-height: 100vh;
  .content {
    text-align: center;
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);

    img {
      width: 100%;
      max-width: 350px !important;
      margin-bottom: 30px;
    }
    h4 {
      margin-bottom: 30px;
    }
    .go-home {
      padding: 7px 30px;
      background-color: #1998E4;
      border-radius: 5px;
      color: white;
      margin-top: 10px;
      cursor: pointer;
      &:hover {
        background-color: #389eda;
      }
    }
  }
}
</style>
<script>
export default {
  name: 'error'
};
</script>
