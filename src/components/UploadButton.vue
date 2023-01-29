<template>
  <div
    class="uploadButton"
    :class="{'normalUploadButtonBackground':!state.monochrome, 'monochromeUploadButtonBackground': state.monochrome}"
    @click="state.manualUpload()"
    title="upload a file - allowed: images, audio files, or a zip containing index.html - max size 50MB"
  ></div>
</template>

<script>

export default{
  name: 'UploadButton',
  props: [ 'state' ]
}
</script>

<style>
.uploadButton{
  position: absolute;
  width: 160px;
  height: 160px;
  background-repeat: no-repeat;
  background-size: 150px 150px;
  background-position: center center;
  border-radius: 50%;
  left: 50%;
  top: 25%;
  cursor: pointer;
  transform: translate(-50%, -75px);
}
.normalUploadButtonBackground{
  box-shadow: 0px 0px 50px 50px #1058;
  background-color: #1058;
  background-image: url(https://jsbot.whitehot.ninja/uploads/lNqcL.png);
}
.monochromeUploadButtonBackground{
  box-shadow: 0px 0px 50px 50px #3338;
  background-color: #3338;
  background-image: url(https://jsbot.whitehot.ninja/uploads/iy1vO.png);
}
</style>