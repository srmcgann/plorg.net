<template>
  <div class="dropModal" :class="{'normalDropModal':!state.monochrome,'monochromeDropModal':state.monochrome}">
    <div class="dropModalInner">
      <div v-if="state.uploadInProgress">
        {{uploadingText}}
      </div>
      <div v-else>
        drop files here
      </div>
    </div>
  </div>
</template>

<script>

  export default{
    name: 'DropModal',
    props: [ 'state'],
    data(){
      return{
      }
    },
    computed:{
      uploadingText(){
        return ('.'.repeat((this.state.globalT*20|0)%8)) + 'uploading' + ('.'.repeat((this.state.globalT*20|0)%8))
      }
    },
    methods:{
    },
    mounted(){
    }
  }
</script>

<style scoped>
.dropModal{
  position: fixed;
  width: 100vw;
  height: 100vh;
  z-index: 200;
  top: 0;
  text-align: center;
}
.dropModalInner{
  width: 500px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 400px;
  font-size: 32px;
}
.normalDropModal{
  background: #487e;
}
.monochromeDropModal{
  background: #666e;
}
</style>

