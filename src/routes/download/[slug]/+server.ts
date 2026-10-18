import fs from 'node:fs';


export async function GET({url})  {
    console.log(url)
    const filename = url.href.split('/')[url.href.split('/').length-1]


    const file = fs.readFileSync('src/routes/download/retrieved/' + filename)
    
    return new Response(file,
        {
           headers: {
               'Content-Type': 'application/octet-stream',
               'Content-Length': file.length.toString(),
               'Content-Disposition': 'attachment; filename=' + filename
           }
        }
    )
}
