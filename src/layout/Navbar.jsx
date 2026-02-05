import { Button } from "@/components/Button";
import { Menu, X } from "lucide-react"; 
import { useState, useEffect } from "react"; // Fix: useEffect toegevoegd aan import

const navLinks = [
  { href: "#about", label: "Over mij" },
  { href: "#projects", label: "Projecten" },
  { href: "#experience", label: "Ervaring" },
  { href: "#testimonials", label: "Testimonials" },
];

export const Navbar = () => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);

  // Fix: useEffect naar binnen verplaatst en logica gecorrigeerd
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <header className={`fixed top-0 right-0 left-0 transition-all duration-300 ${isScrolled ? "bg-background/80 backdrop-blur-md py-3 border-b border-white/5" : "bg-transparent py-5"} z-50`}>
      <nav className="container mx-auto px-6 flex items-center justify-between">
        
        <a href="#" className="text-xl font-bold tracking-tight hover:text-primary transition-colors text-white">
          AA<span className="text-primary">.</span>
        </a>

        {/* Desktop Nav */}
        <div className="hidden md:flex items-center gap-1">
          <div className="bg-white/5 backdrop-blur-md border border-white/10 rounded-full px-2 py-1 flex gap-1 items-center">
            {navLinks.map((link, index) => (
              <a 
                key={index} 
                href={link.href} 
                className="px-4 py-2 text-sm text-muted-foreground hover:text-white rounded-full hover:bg-white/5 transition-all"
              >
                {link.label}
              </a>
            ))}
          </div>
        </div>

        {/* Call to action button */}
        <div className="hidden md:block">
          <Button size="sm">
            <a href="#contact">Neem contact op</a>
          </Button>
        </div>

        {/* Mobile nav - hamburger menu */}
        <button 
          className="md:hidden p-2 text-white transition-colors hover:text-primary cursor-pointer" 
          onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
        >
          {isMobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
        </button>

        {/* mobile menu */}
        {isMobileMenuOpen && (
          <div className="md:hidden absolute top-full left-0 right-0 bg-background/95 backdrop-blur-lg border-b border-white/5">
            <div className="container mx-auto px-6 py-6 flex flex-col gap-4 animate-fade-in">
              {navLinks.map((link, index) => (
                <a 
                  key={index} 
                  href={link.href} 
                  onClick={() => setIsMobileMenuOpen(false)}
                  className="text-lg text-muted-foreground hover:text-white py-2"
                >
                  {link.label}
                </a>
              ))}
              <Button>
                <a href="#contact">Neem contact op</a>
              </Button>
            </div>
          </div>
        )}
      </nav>
    </header>
  );
};