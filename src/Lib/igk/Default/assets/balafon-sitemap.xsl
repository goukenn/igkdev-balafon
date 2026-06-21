<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:igk="http://schemas.igkdev.com/sitemap"
    exclude-result-prefixes="sitemap">
    <xsl:output method="html" encoding="UTF-8" indent="yes" />
    <xsl:template match="/">
        <html lang="fr">
            <head>
                <title>
                    <xsl:value-of select="sitemap:sitemapindex/igk:title" />
                </title>
                <style>
					body {
						font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
						color: #333;
						background-color: #f8f9fa;
						margin: 0;
						padding: 40px 20px;
					}
					.container {
						max-width: 1000px;
						margin: 0 auto;
						background: #ffffff;
						padding: 30px;
						border-radius: 8px;
						box-shadow: 0 4px 6px rgba(0,0,0,0.05);
					}
					h1 {
						color: #1a202c;
						font-size: 24px;
						margin-top: 0;
						margin-bottom: 10px;
						border-bottom: 2px solid #edf2f7;
						padding-bottom: 15px;
					}
					p.description {
						color: #718096;
						font-size: 14px;
						margin-bottom: 30px;
					}
					.stats {
						background-color: #ebf8ff;
						border-left: 4px solid #3182ce;
						color: #2b6cb0;
						padding: 12px;
						margin-bottom: 20px;
						border-radius: 0 4px 4px 0;
						font-size: 14px;
						font-weight: bold;
					}
					table {
						width: 100%;
						border-collapse: collapse;
						text-align: left;
					}
					th {
						background-color: #f7fafc;
						color: #4a5568;
						font-weight: 600;
						padding: 12px 15px;
						border-bottom: 2px solid #e2e8f0;
						font-size: 13px;
						text-transform: uppercase;
						letter-spacing: 0.5px;
					}
					tr:hover {
						background-color: #f7fafc;
					}
					td {
						padding: 12px 15px;
						border-bottom: 1px solid #e2e8f0;
						font-size: 14px;
						word-break: break-all;
					}
					a {
						color: #3182ce;
						text-decoration: none;
					}
					a:hover {
						text-decoration: underline;
					}
					.badge {
						display: inline-block;
						padding: 2px 8px;
						font-size: 11px;
						font-weight: 600;
						background-color: #edf2f7;
						color: #4a5568;
						border-radius: 4px;
					}
				</style>
            </head>
            <body>
                <div class="container">
                    <h1>
                        <xsl:value-of select="sitemap:sitemapindex/igk:title" />
                    </h1>
                    <p class="description"><xsl:value-of select="/sitemap:sitemapindex/igk:description" /></p>

                    <div class="stats"> <xsl:value-of select="sitemap:sitemapindex/igk:rescountingref" /> <xsl:value-of
                            select="count(sitemap:sitemapindex/sitemap:sitemap)" />
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 65%;">
                                    <xsl:choose> 
                                          <xsl:when test="//igk:reslocation">
                                         <xsl:value-of select="/sitemap:sitemapindex/igk:reslocation"></xsl:value-of>
                                    </xsl:when>
                                        <xsl:otherwise> Location (URL) </xsl:otherwise>
                                    </xsl:choose>
                                </th>
                                <th style="width: 25%;">
                                <xsl:choose> 
                                     <xsl:when test="//igk:reslastupdate">
                                         <xsl:value-of select="/sitemap:sitemapindex/igk:reslastupdate"></xsl:value-of>
                                    </xsl:when>
                                        <xsl:otherwise> Last Updated </xsl:otherwise>
                                    </xsl:choose>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                <tr>
                                    <td>
                                        <span class="badge">#<xsl:value-of select="position()" /></span>
                                    </td>
                                    <td>
                                        <a href="{sitemap:loc}">
                                            <xsl:value-of select="sitemap:loc" />
                                        </a>
                                    </td>
                                    <td>
                                        <xsl:value-of select="sitemap:lastmod" />
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>