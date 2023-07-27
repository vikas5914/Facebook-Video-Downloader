const getDownloadLink = async () => {
  $('#result').hide()

  const vid_url = $('#link').val()

  $('#download').val('Grabbing Link ...')
  $('#download').attr('disabled', 'disabled')

  $('#bar').show()

  const formData = new FormData()
  formData.append('url', vid_url)
  const response = await fetch('app/main.php', {
    method: 'POST',
    body: formData
  })

  const res = await response.json()
  if (res.success) {
    $('#bar').hide()
    $('#result').show()

    $('#title').html(res.title)
    $('#source').html(`<a class='text-white' href='${vid_url}'>${vid_url}</a>`)

    $('#links').html('')

    const links = res.links

    links !== undefined && Object.keys(links).forEach(function (key) {
      $('#links').append(`<a class="btn btn-info mr-2" href="${links[key]}" download="${key}.mp4">${key}</a>`)
    })
  } else {
    $('#bar').hide()
    alert(res.message)
  }

  $('#download').val('Download!')
  $('#download').removeAttr('disabled')
}
