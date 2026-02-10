
Add-Type -AssemblyName System.Drawing
try {
    $img = [System.Drawing.Image]::FromFile("assets/images/Logo.png")
    # Target size 342x250
    $newImg = new-object System.Drawing.Bitmap(342, 250)
    $graph = [System.Drawing.Graphics]::FromImage($newImg)
    $graph.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graph.DrawImage($img, 0, 0, 342, 250)
    $newImg.Save("assets/images/logo_optimized.png", [System.Drawing.Imaging.ImageFormat]::Png)
    $img.Dispose()
    $newImg.Dispose()
    $graph.Dispose()
    Write-Host "Logo resized successfully."
} catch {
    Write-Error "Failed to resize logo: $_"
}
