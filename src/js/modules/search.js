export default function(context) {
  const data = context.querySelector('#search_metadata');
  if (data) {
    const parsedData = JSON.parse(data.textContent);
    if ('script_age' in parsedData) {
      window.script_age = parsedData.script_age;
    }
    if ('region_to_block' in parsedData) {
      window.region_to_block = parsedData.region_to_block;
    }
  }
}
