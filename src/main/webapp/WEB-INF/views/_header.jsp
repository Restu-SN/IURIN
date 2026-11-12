<%@ page contentType="text/html;charset=UTF-8" pageEncoding="UTF-8" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="fn" uri="http://java.sun.com/jsp/jstl/functions" %>
<%@ taglib prefix="f" uri="/WEB-INF/iurin.tld" %>
<c:set var="ctx" value="${pageContext.request.contextPath}"/>
<c:set var="klub" value="${initParam.namaKlub}"/>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <title>Iurin &middot; <c:out value="${klub}"/></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="${ctx}/assets/iurin.css?v=${initParam.versiAset}" rel="stylesheet">
</head>
<body>
<header class="masthead">
    <div class="ticker" aria-hidden="true">
        <div class="ticker-track">
            <c:forEach begin="1" end="8">
                <span>Kalo ga bayar gua smash nih</span><span class="sep">&#127992;</span>
                <span><c:out value="${klub}"/></span><span class="sep">&#127992;</span>
            </c:forEach>
        </div>
    </div>
    <div class="mast-inner">
        <a class="wordmark" href="${ctx}/anggota">
            <b>IURIN</b>
            <small><c:out value="${klub}"/><br>Kas &amp; iuran main</small>
        </a>
        <div class="mast-user dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-label="Akun">
                <i class="bi bi-person-square"></i>
                <span class="who"><c:out value="${sessionScope.pengguna.nama}"/></span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-secondary">${isAdmin ? 'Admin' : 'Anggota (hanya lihat)'}</span></li>
                <li><a class="dropdown-item" href="${ctx}/password"><i class="bi bi-key me-2"></i>Ganti password</a></li>
                <li><a class="dropdown-item" href="${ctx}/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
    <nav class="tabs" aria-label="Menu utama">
        <a class="${menu == 'anggota' ? 'active' : ''}" href="${ctx}/anggota"><b>01</b>Anggota</a>
        <a class="${menu == 'pembayaran' ? 'active' : ''}" href="${ctx}/pembayaran"><b>02</b>Catat Main</a>
        <a class="${menu == 'status' ? 'active' : ''}" href="${ctx}/status"><b>03</b>Riwayat</a>
        <a class="${menu == 'kas' ? 'active' : ''}" href="${ctx}/kas"><b>04</b>Rekap Kas</a>
    </nav>
</header>

<main class="content">
    <c:if test="${not empty sessionScope.flash}">
        <div class="alert alert-${sessionScope.flashTipe} alert-dismissible fade show">
            <i class="bi ${sessionScope.flashTipe == 'success' ? 'bi-check-square-fill' : 'bi-exclamation-square'}"></i>
            <span><c:out value="${sessionScope.flash}"/></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <c:remove var="flash" scope="session"/>
        <c:remove var="flashTipe" scope="session"/>
    </c:if>
    <c:if test="${not empty error}">
        <div class="alert alert-danger"><i class="bi bi-exclamation-square-fill"></i> <c:out value="${error}"/></div>
    </c:if>
