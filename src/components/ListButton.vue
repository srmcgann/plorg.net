<template>
  <div
    class="uploadButton"
    :class="{'normalUploadButtonBackground':!state.monochrome, 'monochromeUploadButtonBackground': state.monochrome}"
    @click="state.finalizeListing()"
    title="finalize listing"
  ></div>
</template>

<script>

export default{
  name: 'listButton',
  props: [ 'state' ]
}
</script>

<style scoped>
.listButton{
  position: absolute;
  width: 160px;
  height: 160px;
  background-image: url(https://jsbot.cantelope.org/uploads/lNqcL.png);
  background-repeat: no-repeat;
  background-size: 150px 150px;
  background-position: center center;
  border-radius: 50%;
  background-color: #1058;
  box-shadow: 0px 0px 50px 50px #1058;
  left: 50%;
  top: 25%;
  cursor: pointer;
  transform: translate(-50%, -75px);
}
</style>