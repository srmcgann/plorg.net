import process from 'process';
let args = process.argv
import puppeteer from 'puppeteer';
var url = args[2]
var delay = args[3]
var width = +args[4];
var height = +args[5];
var path = args[6];

function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
};

(async() => {
  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({width, height});
  await page.setRequestInterception(true);
  await page.on('request', async (req) => {
    if(req.resourceType() === 'audio'){
      await req.abort();
      //await req.src = '';
    } else {
      await req.continue();
    }
  })
  await page.goto(url);
  await page.evaluate(async () => {
    let els = document.querySelectorAll('img');
    for(let i = 0; i< els.length; ++i){
      els[i].style.background='#000';
    }
  });
  await timeout(delay)
  await page.screenshot({type: 'png', path});
  await browser.close();
  process.exit()
})();
