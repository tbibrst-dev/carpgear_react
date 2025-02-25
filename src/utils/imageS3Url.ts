const S3_BASE_URL = "https://carpgear.s3-accelerate.amazonaws.com/uploads"; 
const WORDPRESS_MEDIA_PATH = "competition/wp-content/uploads/"; // Part of the URL indicating a WP upload

export const getMediaUrl = (url?: string): string => {
  if (!url) return "";

  // If the URL is already an S3 URL, return it as-is
  if (url.includes(S3_BASE_URL)) {
    return url;
  }

  // If it's a WordPress media URL, extract filename and replace with S3 URL
  if (url.includes(WORDPRESS_MEDIA_PATH)) {
    const fileName = url.split(WORDPRESS_MEDIA_PATH).pop(); 
    return `${S3_BASE_URL}/${fileName}`;
  }

  // If the URL does not match known patterns, return it as is
  return url;
};
