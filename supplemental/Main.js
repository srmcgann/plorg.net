  import process from 'process';
  import bip39 from 'bip39';
  import { TezosToolkit } from '@taquito/taquito';

  const tezos = new TezosToolkit('https://rpc.dweet.net');

  let args = process.argv
  let command = args[2]
  switch(command.toUpperCase()){
    case 'GETBALANCE':
      let addy = args[3]
      tezos.tz
      .getBalance(addy)
      .then((balance) => console.log(`${balance.toNumber() / 1000000} ꜩ`))
      .catch((error) => console.log(JSON.stringify(error)));
    break
    case 'MNEMONIC':
      console.log(bip39.generateMnemonic())
    break
  }

/*
tezos.wallet
  .transfer({ to: 'tz1NhNv9g7rtcjyNsH8Zqu79giY5aTqDDrzB', amount: 0.2 })
  .send()
  .then((op) => {
    println(`Hash: ${op.opHash}`);

    op.confirmation()
      .then((result) => {
        console.log(result);
        if (result.completed) {
          println('Transaction correctly processed!');
        } else {
          println('An error has occurred');
        }
      })
      .catch((err) => println(err));
  });
*/
