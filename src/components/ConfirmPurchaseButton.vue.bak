<template>
  <button
    @click="state.finalizePurchase(item)"
    class="confirmPurchaseButton"
    :class="{'disabledButton': !item.enabled || item.mints >= item.editions}"
    :disabled="!item.enabled || item.mints >= item.editions"
  >
   confirm purchase for {{item.price}} tez
  </button>
  <div class="disclaimer">
    once completed, this transaction cannot be reversed
  </div>
</template>

<script>

export default{
  name: 'ConfirmPurchaseButton',
  props: [ 'state', 'item' ],
}
</script>

<style scoped>
.disclaimer{
  color: #ff0;
  position: fixed;
  text-align: center;
  min-width: 350px;
  display: inline-block;
  background: #4208;
  left: 50%;
  text-shadow: 2px 2px 2px #000;
  padding: 10px;
  font-size: 24px;
  top: calc(50% + 140px);
  transform: translate(-50%, -50%);
}
.confirmPurchaseButton{
  position: fixed;
  z-index: 10000;
  left: 50%;
  top:50%;
  transform: translate(-50%, -50%);
  font-size: 24px;
  border-radius: 2px;
  min-width: 550px;
  padding:20px;
  color: #fff;
  border-radius: 80px;
  border: none!important;
  text-shadow: 2px 2px 2px #000;
  background: #086e;
}
</style>