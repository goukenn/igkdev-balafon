<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:igk="http://schemas.igkdev.com/sitemap"
                exclude-result-prefixes="sitemap igk">
    
    <xsl:output method="html" encoding="UTF-8" indent="yes" />

    <xsl:template match="/">
        <html lang="fr">
            <head>
                <title>
                    <xsl:choose>
                        <xsl:when test="//igk:title"><xsl:value-of select="//igk:title"/></xsl:when>
                        <xsl:otherwise>Balafon - Liste des URLs</xsl:otherwise>
                    </xsl:choose>
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
                        margin-bottom: 5px;
                    }
                    p.description {
                        color: #718096;
                        font-size: 14px;
                        margin-top: 0;
                        margin-bottom: 25px;
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
                    .priority-badge {
                        display: inline-block;
                        padding: 2px 6px;
                        font-size: 11px;
                        font-weight: 600;
                        background-color: #edf2f7;
                        color: #4a5568;
                        border-radius: 4px;
                    }
                    .freq-text {
                        color: #4a5568;
                        font-style: italic;
                        font-size: 13px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>
                        <xsl:choose>
                            <xsl:when test="//igk:title"><xsl:value-of select="//igk:title"/></xsl:when>
                            <xsl:otherwise>Cartographie des URLs</xsl:otherwise>
                        </xsl:choose>
                    </h1>
                    
                    <p class="description">
                        <xsl:choose>
                            <xsl:when test="//igk:description"><xsl:value-of select="//igk:description"/></xsl:when>
                            <xsl:otherwise>Liste exhaustive des routes et adresses indexées pour ce module Balafon.</xsl:otherwise>
                        </xsl:choose>
                    </p>
                    
                    <div class="stats">
                        Total des routes disponibles : <xsl:value-of select="count(//sitemap:url)"/>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 55%;">Adresse (URL)</th>
                                <th style="width: 15%;">Dernière Modif.</th>
                                <th style="width: 15%;">Fréquence</th>
                                <th style="width: 15%;">Priorité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="//sitemap:url">
                                <tr>
                                    <td>
                                        <a href="{sitemap:loc}">
                                            <xsl:value-of select="sitemap:loc"/>
                                        </a>
                                    </td>
                                    <td>
                                        <xsl:value-of select="sitemap:lastmod"/>
                                    </td>
                                    <td>
                                        <span class="freq-text"><xsl:value-of select="sitemap:changefreq"/></span>
                                    </td>
                                    <td>
                                        <span class="priority-badge"><xsl:value-of select="sitemap:priority"/></span>
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